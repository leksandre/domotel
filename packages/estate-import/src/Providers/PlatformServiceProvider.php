<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Providers;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateImport\Platform\Services\Contracts\ImportPlatformService;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Facades\Dashboard;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        ImportPlatformService::class => \Kelnik\EstateImport\Platform\Services\ImportPlatformService::class
    ];

    public function boot(\Orchid\Platform\Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $dashboard->registerResource('stylesheets', mix('/css/app.css', 'vendor/kelnik-estate-import'));
        $dashboard->registerResource('scripts', mix('/js/manifest.js', 'vendor/kelnik-estate-import'));
        $dashboard->registerResource('scripts', mix('/js/app.js', 'vendor/kelnik-estate-import'));
    }

    public function registerMainMenu(): array
    {
        $coreService = resolve(CoreService::class);

        Dashboard::addMenuSubElements('estate', [
            Menu::make(trans('kelnik-estate-import::admin.menu.settings'))
                ->icon('bs.gear')
                ->title(trans('kelnik-estate-import::admin.menu.import'))
                ->route($coreService->getFullRouteName('estateImport.settings'))
                ->active([
                    $coreService->getFullRouteName('estateImport.settings')
                ]),
            Menu::make(trans('kelnik-estate-import::admin.menu.history'))
                ->icon('bs.clock-history')
                ->route($coreService->getFullRouteName('estateImport.history'))
                ->active([
                    $coreService->getFullRouteName('estateImport.history')
                ])
        ]);

        return [];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-estate::admin.menu.title'))
                ->addPermission(
                    EstateImportServiceProvider::MODULE_PERMISSION,
                    trans('kelnik-estate-import::admin.permission')
                )
        ];
    }
}
