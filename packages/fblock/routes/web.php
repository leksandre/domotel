<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\FBlock\Platform\Screens\BlockEditScreen;
use Kelnik\FBlock\Platform\Screens\BlockListScreen;
use Kelnik\FBlock\Providers\FBlockServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . FBlockServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        // Elements
        Route::screen('fblock/list', BlockListScreen::class)
            ->name($coreService->getFullRouteName('fblock.elements'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-fblock::admin.menu.title'))
            );

        Route::screen('fblock/edit/{element?}', BlockEditScreen::class)
            ->name($coreService->getFullRouteName('fblock.element'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-fblock::admin.menu.title'))
                    ->push(trans('kelnik-fblock::admin.menu.elements'))
            );

        Route::patch('fblock/sort', [BlockListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('fblock.sort'));
    });
