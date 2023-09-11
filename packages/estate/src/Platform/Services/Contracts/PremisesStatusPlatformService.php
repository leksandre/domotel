<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;

interface PremisesStatusPlatformService
{
    public function __construct(PremisesStatusRepository $repository, CoreService $coreService);

    public function getList(): Collection;

    public function save(PremisesStatus $premisesStatus, Request $request): RedirectResponse;

    public function remove(PremisesStatus $premisesStatus, string $backRoute): RedirectResponse;
}
