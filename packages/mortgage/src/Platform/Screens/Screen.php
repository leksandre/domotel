<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Mortgage\Platform\Services\Contracts\MortgagePlatformService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected CoreService $coreService;
    protected MortgagePlatformService $mortgagePlatformService;
    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->mortgagePlatformService = resolve(MortgagePlatformService::class);
    }
}
