<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Progress\Platform\Screens\AlbumEditScreen;
use Kelnik\Progress\Platform\Screens\AlbumListScreen;
use Kelnik\Progress\Platform\Screens\CameraEditScreen;
use Kelnik\Progress\Platform\Screens\CameraListScreen;
use Kelnik\Progress\Platform\Screens\GroupEditScreen;
use Kelnik\Progress\Platform\Screens\GroupListScreen;
use Kelnik\Progress\Providers\ProgressServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . ProgressServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        Route::screen('progress/album/list', AlbumListScreen::class)
            ->name($coreService->getFullRouteName('progress.albums'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-progress::admin.menu.title'))
            );

        Route::screen('progress/album/edit/{album?}', AlbumEditScreen::class)
            ->name($coreService->getFullRouteName('progress.album'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-progress::admin.menu.title'))
                    ->push(trans('kelnik-progress::admin.menu.albums'))
            );

        Route::screen('progress/camera/list', CameraListScreen::class)
            ->name($coreService->getFullRouteName('progress.cameras'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-progress::admin.menu.title'))
            );

        Route::screen('progress/camera/edit/{camera?}', CameraEditScreen::class)
            ->name($coreService->getFullRouteName('progress.camera'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-progress::admin.menu.title'))
                    ->push(trans('kelnik-progress::admin.menu.cameras'))
            );

        Route::patch('progress/camera/sort', [CameraListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('progress.cameras.sort'));

        Route::screen('progress/group/list', GroupListScreen::class)
            ->name($coreService->getFullRouteName('progress.groups'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-progress::admin.menu.title'))
            );

        Route::screen('progress/group/edit/{group?}', GroupEditScreen::class)
            ->name($coreService->getFullRouteName('progress.group'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-progress::admin.menu.title'))
                    ->push(trans('kelnik-progress::admin.menu.groups'))
            );
    });
