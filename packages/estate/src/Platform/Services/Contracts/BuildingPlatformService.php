<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Repositories\Contracts\BuildingRepository;

interface BuildingPlatformService
{
    public function __construct(BuildingRepository $repository, CoreService $coreService);

    public function save(Building $building, Request $request): RedirectResponse;

    public function remove(Building $building, string $backRoute): RedirectResponse;
}
