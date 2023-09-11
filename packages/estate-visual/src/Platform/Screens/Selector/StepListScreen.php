<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Screens\Selector;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\Steps\Contracts\Step;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Platform\Layouts\Selector\StepListLayout;
use Kelnik\EstateVisual\Platform\Screens\BaseScreen;
use Kelnik\EstateVisual\Platform\Services\Contracts\SelectorPlatformService;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;

final class StepListScreen extends BaseScreen
{
    private Selector $selector;

    public function query(Selector $selector): array
    {
        $this->name = trans('kelnik-estate-visual::admin.menu.selector');
        $this->selector = $selector;
        $this->selector->load(['complex', 'stepElements']);

        if ($this->selector->exists) {
            $this->name .= ': ' . $selector->title;
        }

        return [
            'selector' => $selector,
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate-visual::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('estateVisual.selector.list')),
        ];
    }

    public function layout(): array
    {
        if (!$this->selector->steps) {
            return [
                Layout::view(
                    'kelnik-estate-visual::platform.layouts.empty',
                    ['headerText' => trans('kelnik-estate-visual::admin.noSteps')]
                )
            ];
        }

        $steps = new Collection();

        foreach ($this->selector->steps as $step) {
            $steps->add(Factory::make($step, $this->selector));
        }

        $i = 1;
        $res = [];
        $stepEstateToElement = [];
        $steps
            ->sortBy(static fn(Step $step) => $step->getPriority())
            ->each(function (Step $step) use (&$res, &$i, &$stepEstateToElement) {
                $groups = null;
                $estateElements = $step->getEstateElements($stepEstateToElement);

                if ($step->adminListAsAccordion()) {
                    $groups = $this->prepareGroups($step, $estateElements);
                }

                $res[] = (new StepListLayout($step, $estateElements, $groups))->title(
                    trans('kelnik-estate-visual::admin.step') . ' ' . $i++ . '. ' . $step->getTitle()
                );
            });

        return $res;
    }

    private function prepareGroups(Step $step, Collection $estateElements): Collection
    {
        $groups = $step->getEstateParentModels($estateElements->pluck('estate_model_id'));
        $prevStepName = $step->getPrevStepName();
        $prevStepModel = $prevStepName
            ? Factory::make($prevStepName, $this->selector)->getEstateModelNamespace()
            : null;

        $isComplexToFloor = $step->getName() === Factory::STEP_FLOOR && $prevStepName === Factory::STEP_COMPLEX;
        $sectionIsFirstStep = $step->getName() === Factory::STEP_SECTION && !$prevStepName;
        $floorIsFirstStep = $step->getName() === Factory::STEP_FLOOR && !$prevStepName;
        $useSpecialHeader = $isComplexToFloor || $sectionIsFirstStep || $floorIsFirstStep;

        $groupIds = $groups->pluck('id')->toArray();
        $groupsParentIds = $isComplexToFloor
            ? []
            : $this->selector->stepElements->filter(
                static fn(StepElement $el) => $el->estate_model === $prevStepModel
                    && in_array($el->estate_model_id, $groupIds)
            )->pluck('id', 'estate_model_id')->toArray();

        $visualParentId = $this->selector->stepElements->first(
            static fn($el) => $el->step === Factory::STEP_COMPLEX
        )?->getKey() ?? 0;

        /** @var EstateModel $el */
        foreach ($groups as &$el) {
            $el->visualElementId = $useSpecialHeader ? $visualParentId : $groupsParentIds[$el->getKey()] ?? -1;

            if (mb_strlen($el->title) >= 2) {
                continue;
            }

            $groupTitle = $el->getAttribute('groupTitle');
            $groupName = $el->getAttribute('groupName');
            $groupTitle = $groupTitle && $groupName
                ? $step->prepareTitle($groupTitle, $groupName) . ', '
                : '';

            $el->title = $groupTitle . $step->prepareTitle(
                $el->title,
                $useSpecialHeader ? Factory::STEP_BUILDING : $step->getPrevStepName()
            );
        }
        unset($el);

        return $groups;
    }

    public function addStepElement(Selector $selector, Request $request): RedirectResponse
    {
        return resolve(SelectorPlatformService::class)->addStepElement($selector, $request);
    }
}
