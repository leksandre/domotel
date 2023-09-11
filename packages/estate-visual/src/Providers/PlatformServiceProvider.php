<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Providers;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\Platform\Services\Contracts\SelectorPlatformService;
use Kelnik\EstateVisual\Platform\Services\Contracts\StepElementPlatformService;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Facades\Dashboard;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        SelectorPlatformService::class => \Kelnik\EstateVisual\Platform\Services\SelectorPlatformService::class,
        StepElementPlatformService::class => \Kelnik\EstateVisual\Platform\Services\StepElementPlatformService::class
    ];

    public function boot(\Orchid\Platform\Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $dashboard->registerResource('stylesheets', mix('/css/app.css', 'vendor/kelnik-estate-visual'));
        $dashboard->registerResource('scripts', mix('/js/manifest.js', 'vendor/kelnik-estate-visual'));
        $dashboard->registerResource('scripts', mix('/js/vendor.js', 'vendor/kelnik-estate-visual'));
        $dashboard->registerResource('scripts', mix('/js/app.js', 'vendor/kelnik-estate-visual'));
    }

    public function registerMainMenu(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        Dashboard::addMenuSubElements('estate', [
            Menu::make(trans('kelnik-estate-visual::admin.menu.selectorList'))
                ->icon('layers')
                ->title(trans('kelnik-estate-visual::admin.menu.selector'))
                ->route($coreService->getFullRouteName('estateVisual.selector.list'))
            ->active([
                $coreService->getFullRouteName('estateVisual.selector.list'),
                $coreService->getFullRouteName('estateVisual.selector.edit'),
                $coreService->getFullRouteName('estateVisual.selector.step.list'),
                $coreService->getFullRouteName('estateVisual.selector.step.edit')
            ])
        ]);

        return [];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-estate::admin.menu.title'))
                ->addPermission(
                    EstateVisualServiceProvider::MODULE_PERMISSION,
                    trans('kelnik-estate-visual::admin.permission')
                )
        ];
    }
}
