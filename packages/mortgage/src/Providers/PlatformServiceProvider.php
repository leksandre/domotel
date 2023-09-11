<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Providers;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Mortgage\Platform\Services\Contracts\MortgagePlatformService;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        MortgagePlatformService::class => \Kelnik\Mortgage\Platform\Services\MortgagePlatformService::class
    ];

    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $dashboard->registerResource('scripts', mix('/js/manifest.js', 'vendor/kelnik-mortgage'));
        $dashboard->registerResource('scripts', mix('/js/app.js', 'vendor/kelnik-mortgage'));
    }

    public function registerMainMenu(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        return [
            Menu::make(trans('kelnik-mortgage::admin.menu.title'))
                ->icon('bs.database')
                ->sort(1210)
                ->active([
                    $coreService->getFullRouteName('mortgage.banks'),
                    $coreService->getFullRouteName('mortgage.bank')
                ])
                ->permission(MortgageServiceProvider::MODULE_PERMISSION)
                ->list([
                    Menu::make('kelnik-mortgage::admin.menu.banks')
                    ->icon('bs.bank')
                    ->route($coreService->getFullRouteName('mortgage.banks'))
                    ->active([
                        $coreService->getFullRouteName('mortgage.banks'),
                        $coreService->getFullRouteName('mortgage.bank')
                    ])
                    ->permission(MortgageServiceProvider::MODULE_PERMISSION)
                ])
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-mortgage::admin.menu.title'))
                ->addPermission(MortgageServiceProvider::MODULE_PERMISSION, trans('kelnik-mortgage::admin.permission'))
        ];
    }
}
