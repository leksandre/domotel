<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureGroupRepository;

interface PremisesFeatureGroupPlatformService
{
    public function __construct(PremisesFeatureGroupRepository $groupRepository, CoreService $coreService);

    public function save(PremisesFeatureGroup $premisesFeatureGroup, Request $request): RedirectResponse;

    public function remove(PremisesFeatureGroup $premisesFeatureGroup, string $backRoute): RedirectResponse;
}
