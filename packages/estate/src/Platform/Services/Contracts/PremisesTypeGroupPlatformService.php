<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;

interface PremisesTypeGroupPlatformService
{
    public function __construct(PremisesTypeGroupRepository $groupRepository, CoreService $coreService);

    public function getList(): array;

    public function save(PremisesTypeGroup $premisesTypeGroup, Request $request): RedirectResponse;

    public function remove(PremisesTypeGroup $premisesTypeGroup, string $backRoute): RedirectResponse;

    public function createLinkToPage(PremisesTypeGroup $premisesTypeGroup, array $sitePages): void;

    public function createSlugByTitle(string $title): string;
}
