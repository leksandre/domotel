<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . EstateServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        // Complex
        Route::screen('estate/complex/list', \Kelnik\Estate\Platform\Screens\Complex\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.complex.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen('estate/complex/edit/{complex?}', \Kelnik\Estate\Platform\Screens\Complex\EditScreen::class)
            ->name($coreService->getFullRouteName('estate.complex.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.complexes'))
            );
        Route::patch('estate/complex/sort', [\Kelnik\Estate\Platform\Screens\Complex\ListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('estate.complex.sort'));

        // Building
        Route::screen('estate/building/list', \Kelnik\Estate\Platform\Screens\Building\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.building.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen('estate/building/edit/{building?}', \Kelnik\Estate\Platform\Screens\Building\EditScreen::class)
            ->name($coreService->getFullRouteName('estate.building.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.buildings'))
            );
        Route::patch('estate/building/sort', [\Kelnik\Estate\Platform\Screens\Building\ListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('estate.building.sort'));

        // Section
        Route::screen('estate/section/list', \Kelnik\Estate\Platform\Screens\Section\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.section.list'))
            ->breadcrumbs(
                static fn(Trail $trail)  => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen('estate/section/edit/{section?}', \Kelnik\Estate\Platform\Screens\Section\EditScreen::class)
            ->name($coreService->getFullRouteName('estate.section.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.sections'))
            );
        Route::patch('estate/section/sort', [\Kelnik\Estate\Platform\Screens\Section\ListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('estate.section.sort'));

        // Floor
        Route::screen('estate/floor/list', \Kelnik\Estate\Platform\Screens\Floor\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.floor.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen('estate/floor/edit/{floor?}', \Kelnik\Estate\Platform\Screens\Floor\EditScreen::class)
            ->name($coreService->getFullRouteName('estate.floor.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.floors'))
            );
        Route::patch('estate/floor/sort', [\Kelnik\Estate\Platform\Screens\Floor\ListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('estate.floor.sort'));


        // Premises
        Route::screen('estate/premises/list', \Kelnik\Estate\Platform\Screens\Premises\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.premises.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen('estate/premises/edit/{premises?}', \Kelnik\Estate\Platform\Screens\Premises\EditScreen::class)
            ->name($coreService->getFullRouteName('estate.premises.edit'))
            ->breadcrumbs(
                static fn(Trail $trail)  => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.premises'))
            );

        // Completion
        Route::screen('estate/completion/list', \Kelnik\Estate\Platform\Screens\Completion\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.completion.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen(
            'estate/completion/edit/{completion?}',
            \Kelnik\Estate\Platform\Screens\Completion\EditScreen::class
        )->name($coreService->getFullRouteName('estate.completion.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.completions'))
            );
        Route::patch(
            'estate/completion/sort',
            [\Kelnik\Estate\Platform\Screens\Completion\ListScreen::class, 'sortable']
        )->name($coreService->getFullRouteName('estate.completion.sort'));

        // Premises status
        Route::screen('estate/pstatus/list', \Kelnik\Estate\Platform\Screens\PremisesStatus\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.pstatus.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen('estate/pstatus/edit/{status?}', \Kelnik\Estate\Platform\Screens\PremisesStatus\EditScreen::class)
            ->name($coreService->getFullRouteName('estate.pstatus.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.premisesStatuses'))
            );
        Route::patch(
            'estate/pstatus/sort',
            [\Kelnik\Estate\Platform\Screens\PremisesStatus\ListScreen::class, 'sortable']
        )->name($coreService->getFullRouteName('estate.pstatus.sort'));

        // Premises plan type
        Route::screen('estate/pplantype/list', \Kelnik\Estate\Platform\Screens\PremisesPlanType\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.pplantype.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen(
            'estate/pplantype/edit/{type?}',
            \Kelnik\Estate\Platform\Screens\PremisesPlanType\EditScreen::class
        )->name($coreService->getFullRouteName('estate.pplantype.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.premisesPlanTypes'))
            );
        Route::patch(
            'estate/pplantype/sort',
            [\Kelnik\Estate\Platform\Screens\PremisesPlanType\ListScreen::class, 'sortable']
        )->name($coreService->getFullRouteName('estate.pplantype.sort'));

        // Premises types
        Route::screen('estate/ptype/list', \Kelnik\Estate\Platform\Screens\PremisesType\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.ptype.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen('estate/ptype/edit/{type?}', \Kelnik\Estate\Platform\Screens\PremisesType\EditScreen::class)
            ->name($coreService->getFullRouteName('estate.ptype.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.premisesTypes'))
            );
        Route::patch(
            'estate/ptype/sort',
            [\Kelnik\Estate\Platform\Screens\PremisesType\ListScreen::class, 'sortable']
        )->name($coreService->getFullRouteName('estate.ptype.sort'));

        // Premises features
        Route::screen('estate/pfeature/list', \Kelnik\Estate\Platform\Screens\PremisesFeature\ListScreen::class)
            ->name($coreService->getFullRouteName('estate.pfeature.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
        Route::screen(
            'estate/pfeature/edit/{feature?}',
            \Kelnik\Estate\Platform\Screens\PremisesFeature\EditScreen::class
        )->name($coreService->getFullRouteName('estate.pfeature.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
                    ->push(trans('kelnik-estate::admin.menu.premisesFeatures'))
            );
        Route::patch(
            'estate/pfeature/sort',
            [\Kelnik\Estate\Platform\Screens\PremisesFeature\ListScreen::class, 'sortable']
        )->name($coreService->getFullRouteName('estate.pfeature.sort'));

        Route::screen('estate/import-plan/list', \Kelnik\Estate\Platform\Screens\ImportPlanScreen::class)
            ->name($coreService->getFullRouteName('estate.importPlan'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-estate::admin.menu.title'))
            );
    });
