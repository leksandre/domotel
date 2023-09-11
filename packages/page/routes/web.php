<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Platform\PageAdminController;
use Kelnik\Page\Platform\Screens\Component\ComponentEditScreen;
use Kelnik\Page\Platform\Screens\Component\ComponentsListScreen;
use Kelnik\Page\Platform\Screens\Page\EditScreen;
use Kelnik\Page\Platform\Screens\Page\ListScreen;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . PageServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        Route::screen('page/{site}/list', ListScreen::class)
            ->name($coreService->getFullRouteName('page.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-page::admin.menuTitle'))
            );

        Route::screen('page/{site}/edit/{page?}', EditScreen::class)
            ->name($coreService->getFullRouteName('page.edit'))
            ->breadcrumbs(static function (Trail $trail) use ($coreService) {
                $page = Route::current()->parameter('page', 0);
                $title = $page?->title ?? trans('kelnik-page::admin.newEntry');

                $trail
                    ->parent('platform.index')
                    ->push(
                        trans('kelnik-page::admin.menuTitle'),
                        route(
                            $coreService->getFullRouteName('page.list'),
                            ['site' => Route::current()->parameter('site')]
                        )
                    )
                    ->push($title);
            });

        Route::patch('page/{site}/{page}/component/sort', [ComponentsListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('page.components.sort'));

        Route::screen('page/{site}/{page}/component/list', ComponentsListScreen::class)
            ->name($coreService->getFullRouteName('page.components'))
            ->breadcrumbs(static function (Trail $trail) use ($coreService) {
                $pageId = Route::current()->parameter('page', 0);
                $title = $pageId
                    ? resolve(PageRepository::class)->findByPrimary($pageId)->title
                    : trans('kelnik-page::admin.newEntry');

                $trail
                    ->parent('platform.index')
                    ->push(
                        trans('kelnik-page::admin.menuTitle'),
                        route(
                            $coreService->getFullRouteName('page.list'),
                            ['site' => Route::current()->parameter('site')]
                        )
                    )
                    ->push(
                        $title,
                        route(
                            $coreService->getFullRouteName('page.edit'),
                            ['site' => Route::current()->parameter('site'), 'page' => $pageId]
                        )
                    )
                    ->push(trans('kelnik-page::admin.componentsTitle'));
            });

        Route::screen('page/{site}/{page}/component/edit/{component}', ComponentEditScreen::class)
            ->name($coreService->getFullRouteName('page.component'))
            ->breadcrumbs(static function (Trail $trail) use ($coreService) {
                $pageId = Route::current()->parameter('page', 0);
                $siteId = Route::current()->parameter('site', 0);

                /** @var PageComponent $component */
                $component = Route::current()->parameter('component');
                $component->load('page');
                $pageTitle = $component->page?->title;

                $componentTitle = class_exists($component->component)
                                    ? $component->component::getTitle()
                                    : trans('admin.newEntry');

                $trail
                    ->parent('platform.index')
                    ->push(
                        trans('kelnik-page::admin.menuTitle'),
                        route(
                            $coreService->getFullRouteName('page.list'),
                            ['site' => $siteId]
                        )
                    )
                    ->push(
                        $pageTitle,
                        route(
                            $coreService->getFullRouteName('page.edit'),
                            ['site' => $siteId, 'page' => $pageId]
                        )
                    )
                    ->push(
                        trans('kelnik-page::admin.componentsTitle'),
                        route(
                            $coreService->getFullRouteName('page.components'),
                            ['site' => $siteId, 'page' => $pageId]
                        )
                    )
                    ->push($componentTitle);
            });
        // Page components list for selection, filtered by page id
        Route::get('page/components-list', [PageAdminController::class, 'componentsList'])
            ->name($coreService->getFullRouteName('page.components.list'));

        // Page or component url
        Route::get('page/components-url', [PageAdminController::class, 'pageOrComponentUrl'])
            ->name($coreService->getFullRouteName('page.components.url'));
    });

// Page routes
//
resolve(PageService::class)->loadPageRoutes();
