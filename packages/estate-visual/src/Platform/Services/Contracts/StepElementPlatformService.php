<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;

interface StepElementPlatformService
{
    public function __construct(StepElementRepository $repository, CoreService $coreService);

    public function save(StepElement $element, Request $request): RedirectResponse;

    public function remove(StepElement $element): RedirectResponse;
}
