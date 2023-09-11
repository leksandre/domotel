<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Services;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use InvalidArgumentException;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateVisual\Http\Resources\Platform\AngleResource;
use Kelnik\EstateVisual\Http\Resources\Platform\ElementResource;
use Kelnik\EstateVisual\Http\Resources\Platform\PremisesResource;
use Kelnik\EstateVisual\Models\Enums\MaskType;
use Kelnik\EstateVisual\Models\Enums\PointerType;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\Steps\Contracts\Step;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\ComplexRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesRepository;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;
use Orchid\Support\Facades\Toast;

final class SelectorPlatformService implements Contracts\SelectorPlatformService
{
    public function __construct(
        private SelectorRepository $repository,
        private StepElementRepository $stepElementRepository,
        private CoreService $coreService
    ) {
    }

    public function getStepNumber(string $stepName, Selector $selector): int
    {
        $steps = collect($selector->steps);

        if (!$steps->isNotEmpty() || !$selector->stepIsAllow($stepName)) {
            return 0;
        }

        $number = 0;
        $steps->map(static fn($stepName) => Factory::make($stepName, $selector))
            ->sortBy(static fn(Step $step) => $step->getPriority())
            ->first(static function (Step $step) use ($stepName, &$number) {
                $number++;
                return $step->getName() === $stepName;
            });

        return $number;
    }

    public function save(Selector $selector, Request $request): RedirectResponse
    {
        $request->validate([
            'selector.title' => 'required|max:255',
            'selector.active' => 'boolean',
            'selector.complex_id' => $selector->exists ? 'nullable|numeric' : 'required|numeric',
            'selector.settings_.colors' => 'nullable|array',
            'selector.steps' => $selector->exists ? 'nullable|array' : 'required|array'
        ]);

        $data = Arr::get($request->only('selector'), 'selector');

        if (!empty($data['steps'])) {
            $data['steps'] = array_values(
                array_intersect(array_keys(Factory::STEP_TO_CLASS), array_keys($data['steps']))
            );
        }

        $selector->fill(Arr::except($data, ['complex_id', 'settings_']));
        $selector->complex()->disassociate();
        $selector->settings = new Collection([
            'colors' => Arr::get($data, 'settings_.colors', [])
        ]);

        $complex = !empty($data['complex_id'])
            ? resolve(ComplexRepository::class)->findByPrimary($data['complex_id'])
            : null;

        if ($complex && $complex->exists) {
            $selector->complex()->associate($complex);
        }
        unset($data);

        $addStepElements = !$selector->exists;

        $this->repository->save($selector);

        Toast::info(trans('kelnik-estate-visual::admin.saved'));

        if ($addStepElements) {
            $this->addStepElements($selector, $request->input('selector.steps', []));
        }

        return redirect()->route($this->coreService->getFullRouteName('estateVisual.selector.list'));
    }

    public function remove(Selector $selector): RedirectResponse
    {
        $this->repository->delete($selector)
            ? Toast::info(trans('kelnik-estate-visual::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('estateVisual.selector.list'));
    }

    /**
     * Add new step element to exists selector
     *
     * @param Selector $selector
     * @param Request $request
     * @return RedirectResponse
     */
    public function addStepElement(Selector $selector, Request $request): RedirectResponse
    {
        $step = $request->get('step');

        if ($step) {
            $step = Crypt::decryptString($step);
        }

        if (!$selector->stepIsAllow($step)) {
            Toast::error(trans('kelnik-estate-visual::admin.errors.stepNotAllowed'));

            return back();
        }

        try {
            $step = Factory::make($step, $selector);
        } catch (InvalidArgumentException $e) {
            Toast::error(trans('kelnik-estate-visual::admin.errors.stepNotFound'));

            return back();
        }

        try {
            $stepElement = $step->createStepElement((int)$request->get('model_id'), (int)$request->get('parent_id'));
        } catch (Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }

        Toast::info(trans('kelnik-estate-visual::admin.saved'));

        return redirect()->route(
            $this->coreService->getFullRouteName('estateVisual.selector.step.list'),
            $selector
        );
    }

    /**
     * Create steps for new selector
     *
     * @param Selector $selector
     * @param array $steps
     * @return bool
     */
    public function addStepElements(Selector $selector, array $steps): bool
    {
        if (!$selector->exists) {
            return false;
        }

        $steps = array_values(
            array_intersect(array_keys(Factory::STEP_TO_CLASS), array_keys($steps))
        );

        if (!$steps) {
            return false;
        }

        $stepElements = [];
        $prevStep = null;

        foreach ($steps as $stepName) {
            $step = Factory::make($stepName, $selector);
            $stepElements = $step->createStepElements($stepElements, $prevStep);

            $prevStep = $step;
            unset($step);
        }

        return true;
    }

    public function getBuilderData($selectorId, $id, Request $request): array
    {
        if (!$selectorId || !$id) {
            throw new InvalidArgumentException('Element not found');
        }

        $stepEl = $this->stepElementRepository->findByPrimaryWithAngles($selectorId, $id);

        if (!$stepEl->exists) {
            return throw new InvalidArgumentException('Element not found');
        }

        $data = [
            'angles' => [],
            'curStep' => $stepEl->step,
            'nextStep' => null,
            'steps' => [],
            'elements' => [],
            'titles' => [],
            'pointerTypes' => array_map(
                fn($el) => ['name' => $el->name(), 'title' => $el->title()],
                PointerType::cases()
            )
        ];

        if ($stepEl->relationLoaded('angles') && $stepEl->angles->isEmpty()) {
            $stepEl->angles->add(
                new StepElementAngle(['title' => trans('kelnik-estate-visual::admin.visual.angle') . ' 1'])
            );
        }

        $stepEl->angles->each(function (StepElementAngle $angle) use (&$data, $request) {
            $data['angles'][] = AngleResource::make($angle)->toArray($request);
        });

        $data['steps'] = $this->getSteps($stepEl->selector);
        $curStep = $data['steps']->first(static fn(array $step) => $step['name'] === $stepEl->step);
        $nextSteps = $curStep['next'] ?? [];

        $done = false;
        foreach ($data['steps'] as $step) {
            if (!$done && in_array($step['name'], $nextSteps)) {
                $data['elements'] = $step['elements'];
                $data['nextStep'] = $step['name'];
                $done = true;

                // Filter child nodes
                //
                $group = !empty($step['groups'])
                    ? $step['groups']->first(
                        fn(EstateModel $sectEl) => $sectEl->getKey() === $stepEl->estate_model_id
                            && $sectEl::class === $stepEl->estate_model
                    )
                    : null;

                foreach ($data['elements'] as $k => $el) {
                    if (
                        ($el->parent_id && $el->parent_id !== $stepEl->getKey())
                        || ($group && !in_array($el->estate_model_id, $group->elementIds))
                    ) {
                        unset($data['elements'][$k]);
                    }
                }

                usort($data['elements'], fn($a, $b) => strnatcmp($a->title, $b->title));
            }

            if (empty($step['elements'])) {
                continue;
            }

            foreach ($step['elements'] as $el) {
                $data['titles'][$step['name']][$el['id']] = $el['title'];
            }
            unset($el);
        }

        if ($curStep['canLinkToPremises']) {
            $estateService = resolve(EstateService::class);
            $data['nextStep'] = 'premises';
            $data['elements'] = resolve(PremisesRepository::class)
                ->getByFloorPrimary([$stepEl->estate_model_id])
                ->map(static fn(Premises $premises) =>
                    PremisesResource::make($premises, $estateService)->toArray($request));

            foreach ($data['elements'] as $el) {
                $data['titles'][MaskType::Premises->value][$el['id']] = $el['title'];
            }
        }

        return $data;
    }

    public function getContentLink(int|string $selectorKey = 0): Field
    {
        $routeName = $this->coreService->getFullRouteName('estateVisual.selector.list');
        $params = [];

        if ($selectorKey) {
            $routeName = $this->coreService->getFullRouteName('estateVisual.selector.edit');
            $params['selector'] = $selectorKey;
        }

        return Link::make(trans('kelnik-estate-visual::admin.visual.selectorLink'))
            ->route($routeName, $params)
            ->icon('info')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px');
    }

    private function getSteps(Selector $selector): Collection
    {
        $selector->load('stepElements');

        $res = new Collection();
        $steps = new Collection();

        foreach ($selector->steps as $step) {
            $steps->add(Factory::make($step, $selector));
        }

        $steps->sortBy(static fn(Step $step) => $step->getPriority())
            ->each(static function (Step $step) use ($res, $selector) {
                $elements = $step->getNextStepElements();
                $res->add([
                    'name' => $step->getName(),
                    'next' => array_values(
                        array_intersect($step->getAllowedNext(), $selector->steps)
                    ),
                    'title' => $step->getTitle(),
                    'canLinkToPremises' => $step->maskCanLinkToPremises(),
                    'groups' => $step->adminListAsAccordion()
                        ? $step->getEstateParentModels($elements->pluck('estate_model_id'))
                        : null,
                    'elements' => array_values(
                        $elements->map(static fn(StepElement $el) => ElementResource::make($el))->toArray()
                    )
                ]);
            });

        return $res;
    }
}
