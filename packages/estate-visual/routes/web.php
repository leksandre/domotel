<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\Http\Controllers\Platform\VisualBuilderController;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Platform\Screens\Selector\EditScreen;
use Kelnik\EstateVisual\Platform\Screens\Selector\ListScreen;
use Kelnik\EstateVisual\Platform\Screens\Selector\StepListScreen;
use Kelnik\EstateVisual\Platform\Services\Contracts\SelectorPlatformService;
use Kelnik\EstateVisual\Providers\EstateVisualServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . EstateVisualServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        Route::screen('estate/visual/selector/list', ListScreen::class)
            ->name($coreService->getFullRouteName('estateVisual.selector.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate-visual::admin.menu.title'))
            );
        Route::screen('estate/visual/selector/edit/{selector?}', EditScreen::class)
            ->name($coreService->getFullRouteName('estateVisual.selector.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate-visual::admin.menu.title'))
                    ->push(trans('kelnik-estate-visual::admin.menu.selector'))
            );

        Route::screen('estate/visual/selector/steps/{selector?}/list', StepListScreen::class)
            ->name($coreService->getFullRouteName('estateVisual.selector.step.list'))
            ->breadcrumbs(
                static fn (Trail $trail) => $trail->parent('platform.index')
                    ->push(
                        trans('kelnik-estate-visual::admin.menu.selectorListTitle'),
                        route($coreService->getFullRouteName('estateVisual.selector.list'))
                    )
                    ->push(Route::current()->parameter('selector')?->title ?? '')
            );

        Route::screen(
            'estate/visual/selector/steps/{selector}/edit/{element}',
            \Kelnik\EstateVisual\Platform\Screens\StepElement\EditScreen::class
        )
            ->name($coreService->getFullRouteName('estateVisual.selector.step.edit'))
            ->breadcrumbs(static function (Trail $trail) use ($coreService) {
                $stepElement = Route::current()->parameter('stepElement');
                $selector = Route::current()->parameter('selector');

                $res = $trail
                    ->parent('platform.index')
                    ->push(
                        trans('kelnik-estate-visual::admin.menu.selectorListTitle'),
                        route($coreService->getFullRouteName('estateVisual.selector.list'))
                    );

                if (!$stepElement || !$selector) {
                    return $res;
                }

                $step = Factory::make($stepElement->step, $stepElement->selector);
                $stepNumber = resolve(SelectorPlatformService::class)->getStepNumber($step->getName(), $selector);

                return $res
                    ->push(
                        $selector->title ?? '-',
                        \route(
                            $coreService->getFullRouteName('estateVisual.selector.step.list'),
                            $selector
                        )
                    )
                    ->push(
                        trans('kelnik-estate-visual::admin.step') . ' ' . $stepNumber .
                        '. ' . $step->getTitle()
                    );
            });

        Route::get(
            'estate/visual/selector/steps/{selector}/data/{element}',
            [VisualBuilderController::class, 'getData']
        )->name($coreService->getFullRouteName('estateVisual.selector.step.data'));
    });
