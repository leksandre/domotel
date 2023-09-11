<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\News\Platform\Screens\CategoryEditScreen;
use Kelnik\News\Platform\Screens\CategoryElementEditScreen;
use Kelnik\News\Platform\Screens\CategoryElementListScreen;
use Kelnik\News\Platform\Screens\CategoryListScreen;
use Kelnik\News\Platform\Screens\ElementEditScreen;
use Kelnik\News\Platform\Screens\ElementListScreen;
use Kelnik\News\Providers\NewsServiceProvider;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . NewsServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        // Categories
        Route::screen('news/category/list', CategoryListScreen::class)
            ->name($coreService->getFullRouteName('news.categories'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail
                    ->parent('platform.index')
                    ->push(trans('kelnik-news::admin.menu.title'))
            );

        Route::screen('news/category/edit/{category?}', CategoryEditScreen::class)
            ->name($coreService->getFullRouteName('news.category'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail
                    ->parent('platform.index')
                    ->push(trans('kelnik-news::admin.menu.title'))
                    ->push(trans('kelnik-news::admin.menu.categories'))
            );

        // Elements
        Route::screen('news/list', ElementListScreen::class)
            ->name($coreService->getFullRouteName('news.elements'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail
                    ->parent('platform.index')
                    ->push(trans('kelnik-news::admin.menu.title'))
            );

        Route::screen('news/edit/{element?}', ElementEditScreen::class)
            ->name($coreService->getFullRouteName('news.element'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail
                    ->parent('platform.index')
                    ->push(trans('kelnik-news::admin.menu.title'))
                    ->push(trans('kelnik-news::admin.menu.elements'))
            );

        Route::screen('news/category/{category}/list', CategoryElementListScreen::class)
            ->name($coreService->getFullRouteName('news.category.elements'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail
                    ->parent('platform.index')
                    ->push(trans('kelnik-news::admin.menu.title'))
            );

        Route::screen('news/category/{category}/edit/{element?}', CategoryElementEditScreen::class)
            ->name($coreService->getFullRouteName('news.category.element'))
            ->breadcrumbs(static function (Trail $trail) {
                $categoryId = (int)Route::current()->parameter('category_id', 0);
                $category = resolve(CategoryRepository::class)->findByPrimary($categoryId);

                return $trail
                    ->parent('platform.index')
                    ->push(trans('kelnik-news::admin.menu.title'))
                    ->push($category->exists ? $category->title : trans('kelnik-news::admin.menu.title'));
            });
    });
