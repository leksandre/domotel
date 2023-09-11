<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;

interface PremisesPlatformService
{
    public function __construct(PremisesRepository $repository, CoreService $coreService);

    public function save(Premises $premises, Request $request): RedirectResponse;

    public function remove(Premises $premises, string $backRoute): RedirectResponse;
}
