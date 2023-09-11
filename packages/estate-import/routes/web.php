<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateImport\Platform\Screens\History\ListScreen;
use Kelnik\EstateImport\Platform\Screens\Settings\SettingsScreen;
use Kelnik\EstateImport\Providers\EstateImportServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . EstateImportServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        $coreService = resolve(CoreService::class);

        Route::screen('estate/import/history', ListScreen::class)
            ->name($coreService->getFullRouteName('estateImport.history'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(
                        trans('kelnik-estate::admin.menu.title'),
                        $coreService->getFullRouteName('estate.complex.list')
                    )
                    ->push(trans('kelnik-estate-import::admin.menu.history'))
            );

        Route::screen('estate/import/settings', SettingsScreen::class)
            ->name($coreService->getFullRouteName('estateImport.settings'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(
                        trans('kelnik-estate::admin.menu.title'),
                        $coreService->getFullRouteName('estate.complex.list')
                    )
                    ->push(trans('kelnik-estate-import::admin.menu.settings'))
            );

        Route::get('estate/import/getlog/{logName}', [ListScreen::class, 'getLog'])
            ->name($coreService->getFullRouteName('estateImport.getLog'))
            ->where(['logName' => '([a-z0-9\-]+)\.log']);
    });
