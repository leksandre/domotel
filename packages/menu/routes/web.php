<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Menu\Platform\Screens\EditScreen;
use Kelnik\Menu\Platform\Screens\ListScreen;
use Kelnik\Menu\Providers\MenuServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . MenuServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        Route::screen('menu/list', ListScreen::class)
            ->name($coreService->getFullRouteName('menu.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-menu::admin.menu.title'))
            );

        Route::screen('menu/edit/{menu?}', EditScreen::class)
            ->name($coreService->getFullRouteName('menu.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-menu::admin.menu.title'))
            );
    });
