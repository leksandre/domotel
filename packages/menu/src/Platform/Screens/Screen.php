<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Menu\Platform\Services\Contracts\MenuPlatformService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected readonly CoreService $coreService;
    protected readonly MenuPlatformService $menuPlatformService;
    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->menuPlatformService = resolve(MenuPlatformService::class);
    }
}
