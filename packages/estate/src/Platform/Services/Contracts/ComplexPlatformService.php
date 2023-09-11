<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Repositories\Contracts\ComplexRepository;

interface ComplexPlatformService
{
    public function __construct(ComplexRepository $repository, CoreService $coreService);

    public function save(Complex $complex, Request $request): RedirectResponse;

    public function remove(Complex $complex, string $backRoute): RedirectResponse;
}
