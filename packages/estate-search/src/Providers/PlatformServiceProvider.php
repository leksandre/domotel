<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Providers;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $dashboard->registerResource('stylesheets', mix('/css/app.css', 'vendor/kelnik-estate-search'));
    }

    public function registerMainMenu(): array
    {
        return [];
    }

    public function registerPermissions(): array
    {
        return [
//            ItemPermission::group(trans('kelnik-estate::admin.menu.title'))
//                ->addPermission(
//                    EstateSearchServiceProvider::MODULE_PERMISSION,
//                    trans('kelnik-estate-search::admin.permission')
//                )
        ];
    }
}
