<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesPlanType;
use Kelnik\Estate\Repositories\Contracts\PremisesPlanTypeRepository;

interface PremisesPlanTypePlatformService
{
    public function __construct(PremisesPlanTypeRepository $repository, CoreService $coreService);

    public function getList(): Collection;

    public function save(PremisesPlanType $premisesPlanType, Request $request): RedirectResponse;

    public function remove(PremisesPlanType $premisesPlanType, string $backRoute): RedirectResponse;

    public function createSlugByTitle(string $title): string;
}
