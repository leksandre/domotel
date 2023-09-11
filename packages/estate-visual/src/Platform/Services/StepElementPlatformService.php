<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\Events\SelectorEvent;
use Kelnik\EstateVisual\Models\Contracts\Position;
use Kelnik\EstateVisual\Models\Enums\MaskType;
use Kelnik\EstateVisual\Models\Enums\PointerType;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\StepElementAnglePointer;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleMaskRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAnglePointerRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;
use Orchid\Support\Facades\Toast;

final class StepElementPlatformService implements Contracts\StepElementPlatformService
{
    public function __construct(private StepElementRepository $repository, private CoreService $coreService)
    {
    }

    public function save(StepElement $element, Request $request): RedirectResponse
    {
        $request->validate([
            'element.title' => 'required|max:255'
        ]);

        $element->fill(Arr::get($request->only('element'), 'element'));

        $saved = $this->repository->save($element);

        if ($saved) {
            $element->load(['angles', 'angles.masks', 'angles.pointers']);
            $requestAngles = Arr::get($request->only('angles'), 'angles') ?? [];
            $currentAngles = $element->angles->pluck(null, 'id');
            $deletingMasks = new Collection();
            $deletingPointers = new Collection();

            $angleRepository = resolve(StepElementAngleRepository::class);
            $maskRepository = resolve(StepElementAngleMaskRepository::class);
            $pointerRepository = resolve(StepElementAnglePointerRepository::class);

            foreach ($requestAngles as $angleKey => $angleData) {
                $angle = $currentAngles[$angleKey] ?? new StepElementAngle();
                $curMasks = new Collection();
                $curPointers = new Collection();

                $angleData['shift'] = $angleData['shift']
                    ? array_map('intval', $angleData['shift'])
                    : [0, 0];

                if ($angle->exists) {
                    $currentAngles->forget($angleKey);
                    $curMasks = $angle->masks->pluck(null, 'id');
                    $curPointers = $angle->pointers->pluck(null, 'id');
                }

                $angle->fill(Arr::except($angleData, ['masks', 'pointers']));
                $angle->element()->associate($element);
                $angleRepository->save($angle);

                $newMasks = $angleData['masks'] ?? [];

                foreach ($newMasks as $maskKey => $maskData) {
                    $mask = $curMasks[$maskKey] ?? new StepElementAngleMask();
                    $elementId = $maskData['element_id'] ?? 0;

//                    if (!$elementId) {
//                        continue;
//                    }

                    if ($mask->exists) {
                        $curMasks->forget($maskKey);
                    }
                    $isPremises = $maskData['type'] === MaskType::Premises->value;

                    $maskData['pointer'] = $this->makePositionByArray($maskData['pointer'] ?? null);

                    $mask->fill(Arr::except($maskData, ['element_id']));

                    if (!$isPremises) {
                        $mask->element()->associate($elementId);
                    } else {
                        $mask->premises()->associate($elementId);
                    }

                    $mask->angle()->associate($angle);
                    $maskRepository->save($mask);
                }
                $deletingMasks = $deletingMasks->merge($curMasks);

                $newPointers = $angleData['pointers'] ?? [];
                foreach ($newPointers as $pKey => $pData) {
                    $pointer = $curPointers[$pKey] ?? new StepElementAnglePointer();

                    if ($pointer->exists) {
                        $curPointers->forget($pKey);
                    }

                    $pointer->type = PointerType::tryFromName($pData['type'] ?? '') ?? PointerType::Text;

                    $pData['position'] = $this->makePositionByArray($pData['position'] ?? null);
                    $pData['data'] = $this->makePointerData($pointer->type, $pData['data'] ?? []);
                    unset($pData['type']);

                    $pointer->fill($pData);

                    $pointer->angle()->associate($angle);
                    $pointerRepository->save($pointer);
                }
                $deletingPointers = $deletingPointers->merge($curPointers);
            }

            $currentAngles->each(static fn(StepElementAngle $el) => $angleRepository->delete($el));
            $deletingMasks->each(static fn(StepElementAngleMask $el) => $maskRepository->delete($el));
            $deletingPointers->each(static fn(StepElementAnglePointer $el) => $pointerRepository->delete($el));
        }

        Toast::info(trans('kelnik-estate-visual::admin.saved'));

        $this->dispatchEvent($element);

        return redirect()->route(
            $this->coreService->getFullRouteName('estateVisual.selector.step.list'),
            ['selector' => $element->selector_id]
        );
    }

    public function remove(StepElement $element): RedirectResponse
    {
        $this->repository->delete($element)
            ? Toast::info(trans('kelnik-estate-visual::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route(
            $this->coreService->getFullRouteName('estateVisual.selector.step.list'),
            ['selector' => $element->selector_id]
        );
    }

    private function makePositionByArray(?array $position): Position
    {
        $position = $position === null
            ? [0, 0]
            : array_map('intval', $position);

        return resolve(
            Position::class,
            [
                'left' => $position[0] ?? 0,
                'top' => $position[1] ?? 0
            ]
        );
    }

    private function makePointerData(PointerType $type, ?array $data): array
    {
        $fields = [
            PointerType::Text->name() => ['text'],
            PointerType::Panorama->name() => ['uid']
        ];

        return Arr::only($data ?? [], $fields[$type->name()]);
    }

    private function dispatchEvent(StepElement $element): void
    {
        $selector = new Selector();
        $selector->id = $element->selector_id;
        SelectorEvent::dispatch($selector, ModelEvent::UPDATED);
    }
}
