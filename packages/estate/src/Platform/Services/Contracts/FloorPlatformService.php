<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Repositories\Contracts\FloorRepository;

interface FloorPlatformService
{
    public function __construct(FloorRepository $repository, CoreService $coreService);

    public function save(Floor $floor, Request $request): RedirectResponse;

    public function remove(Floor $floor, string $backRoute): RedirectResponse;
}
