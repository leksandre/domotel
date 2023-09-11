<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;
use Orchid\Screen\Field;

interface SelectorPlatformService
{
    public function __construct(
        SelectorRepository $repository,
        StepElementRepository $stepElementRepository,
        CoreService $coreService
    );

    public function getStepNumber(string $stepName, Selector $selector): int;

    public function save(Selector $selector, Request $request): RedirectResponse;

    public function remove(Selector $selector): RedirectResponse;

    public function addStepElement(Selector $selector, Request $request): RedirectResponse;

    public function addStepElements(Selector $selector, array $steps): bool;

    public function getBuilderData($selectorId, $id, Request $request): array;

    public function getContentLink(int|string $selectorKey = 0): Field;
}
