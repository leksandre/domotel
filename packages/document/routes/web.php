<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Document\Platform\Screens\CategoryEditScreen;
use Kelnik\Document\Platform\Screens\CategoryListScreen;
use Kelnik\Document\Platform\Screens\GroupEditScreen;
use Kelnik\Document\Platform\Screens\GroupListScreen;
use Kelnik\Document\Providers\DocumentServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . DocumentServiceProvider::MODULE_PERMISSION])
    ->group(function () {

        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        // Categories
        Route::screen('document/category/list', CategoryListScreen::class)
            ->name($coreService->getFullRouteName('document.categories'))
            ->breadcrumbs(
                static fn (Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-document::admin.menu.title'))
            );

        Route::screen('document/category/edit/{category?}', CategoryEditScreen::class)
            ->name($coreService->getFullRouteName('document.category'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-document::admin.menu.title'))
                    ->push(trans('kelnik-document::admin.menu.categories'))
            );

        Route::patch('document/category/sort', [CategoryListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('document.category.sort'));

        Route::screen('document/group/list', GroupListScreen::class)
            ->name($coreService->getFullRouteName('document.groups'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-document::admin.menu.title'))
            );

        Route::screen('document/group/edit/{group?}', GroupEditScreen::class)
            ->name($coreService->getFullRouteName('document.group'))
            ->breadcrumbs(
                static fn (Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-document::admin.menu.title'))
                    ->push(trans('kelnik-document::admin.menu.groups'))
            );
    });
