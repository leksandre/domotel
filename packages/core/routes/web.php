<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Http\Controllers\FileController;
use Kelnik\Core\Platform\Screens\InfoScreen;
use Kelnik\Core\Platform\Screens\SettingsScreen;
use Kelnik\Core\Platform\Screens\Site\EditScreen;
use Kelnik\Core\Platform\Screens\Site\ListScreen;
use Kelnik\Core\Platform\Screens\ToolsScreen;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SiteService;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . CoreServiceProvider::SETTING_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        Route::screen('settings', SettingsScreen::class)
            ->name($coreService->getFullRouteName('core.settings'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(
                        trans('kelnik-core::admin.settings.menu'),
                        route($coreService->getFullRouteName('core.settings'))
                    )
            );
        Route::prefix('about')
            ->group(function () use ($coreService) {
                Route::screen('', InfoScreen::class)
                    ->name($coreService->getFullRouteName('core.about'))
                    ->breadcrumbs(
                        static fn(Trail $trail) => $trail->parent('platform.index')
                            ->push(
                                trans('kelnik-core::admin.about.title'),
                                route($coreService->getFullRouteName('core.about'))
                            )
                    );
                Route::middleware('platform.access:' . CoreServiceProvider::DEVELOPER_PERMISSION)
                    ->name($coreService->getFullRouteName('core.about.php'))
                    ->get('phpinfo', function () {
                        ob_start();
                        phpinfo();
                        $html = ob_get_contents();
                        ob_get_clean();

                        return $html;
                    });
            });

        // Dev tools
        Route::screen('tools', ToolsScreen::class)
            ->middleware('platform.access:' . CoreServiceProvider::DEVELOPER_PERMISSION)
            ->name($coreService->getFullRouteName('core.tools'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(
                        trans('kelnik-core::admin.tools.title'),
                        route($coreService->getFullRouteName('core.tools'))
                    )
            );

        // Sites
        Route::screen('site/list', ListScreen::class)
            ->name($coreService->getFullRouteName('site.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-core::admin.site.menu'))
            );
        Route::screen('site/edit/{site?}', EditScreen::class)
            ->name($coreService->getFullRouteName('site.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-core::admin.site.menu'))
            );

        Route::prefix('core')->group(function () use ($coreService) {
            Route::post('files', [FileController::class, 'uploadChunk'])
                ->name($coreService->getFullRouteName('core.file.chunk.upload'));
        });
    });

// robots.txt
resolve(SiteService::class)->getSeoRobotsRoutes();
