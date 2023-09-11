<?php

declare(strict_types=1);

namespace Kelnik\Menu\Providers;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Menu\Platform\Services\Contracts\MenuPlatformService;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        MenuPlatformService::class => \Kelnik\Menu\Platform\Services\MenuPlatformService::class
    ];

    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $dashboard->registerResource('stylesheets', mix('/css/app.css', 'vendor/kelnik-menu'));
        $dashboard->registerResource('scripts', mix('/js/manifest.js', 'vendor/kelnik-menu'));
        $dashboard->registerResource('scripts', mix('/js/vendor.js', 'vendor/kelnik-menu'));
        $dashboard->registerResource('scripts', mix('/js/app.js', 'vendor/kelnik-menu'));
    }

    public function registerMainMenu(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        return [
            Menu::make(trans('kelnik-menu::admin.menu.title'))
                ->icon('bs.database')
                ->sort(1410)
                ->active([
                    $coreService->getFullRouteName('menu.list'),
                    $coreService->getFullRouteName('menu.edit')
                ])
                ->permission(MenuServiceProvider::MODULE_PERMISSION)
                ->list([
                    Menu::make('kelnik-menu::admin.menu.menus')
                        ->icon('bs.list')
                        ->route($coreService->getFullRouteName('menu.list'))
                        ->active([
                            $coreService->getFullRouteName('menu.list'),
                            $coreService->getFullRouteName('menu.edit')
                        ])
                        ->permission(MenuServiceProvider::MODULE_PERMISSION)
                ])
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-menu::admin.menu.title'))
                ->addPermission(MenuServiceProvider::MODULE_PERMISSION, trans('kelnik-menu::admin.permission'))
        ];
    }
}
