<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Form\Platform\Screens\FieldEditScreen;
use Kelnik\Form\Platform\Screens\FieldListScreen;
use Kelnik\Form\Platform\Screens\FormEditScreen;
use Kelnik\Form\Platform\Screens\FormListScreen;
use Kelnik\Form\Platform\Screens\LogFullListScreen;
use Kelnik\Form\Platform\Screens\LogFullViewScreen;
use Kelnik\Form\Platform\Screens\LogListScreen;
use Kelnik\Form\Platform\Screens\LogViewScreen;
use Kelnik\Form\Providers\FormServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . FormServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        Route::screen('form/list', FormListScreen::class)
            ->name($coreService->getFullRouteName('form.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-form::admin.menu.title'))
            );

        Route::screen('form/edit/{form?}', FormEditScreen::class)
            ->name($coreService->getFullRouteName('form.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-form::admin.menu.title'))
            );

        Route::screen('form/{form:id}/field/list', FieldListScreen::class)
            ->name($coreService->getFullRouteName('form.field.list'))
            ->breadcrumbs(static function (Trail $trail) use ($coreService) {
                $form = Route::current()->parameter('form');

                return $trail
                    ->parent('platform.index')
                    ->push(
                        trans('kelnik-form::admin.menu.title'),
                        route($coreService->getFullRouteName('form.list'))
                    )
                    ->push(
                        $form->title,
                        route($coreService->getFullRouteName('form.edit'), $form)
                    )
                    ->push(trans('kelnik-form::admin.menu.fields'));
            });

        Route::screen('form/{form:id}/field/edit/{field:id}', FieldEditScreen::class)
            ->name($coreService->getFullRouteName('form.field.edit'))
            ->breadcrumbs(static function (Trail $trail) use ($coreService) {
                $form = Route::current()->parameter('form');

                return $trail
                    ->parent('platform.index')
                    ->push(
                        trans('kelnik-form::admin.menu.title'),
                        route($coreService->getFullRouteName('form.list'))
                    )
                    ->push(
                        $form->title,
                        route($coreService->getFullRouteName('form.edit'), $form)
                    )
                    ->push(trans('kelnik-form::admin.menu.fieldEdit'));
            });

        Route::patch('form/{form:id}/field/sort', [FieldListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('form.field.sort'));

        // Form logs
        Route::screen('form/{form:id}/log/list', LogListScreen::class)
            ->name($coreService->getFullRouteName('form.log.list'))
            ->breadcrumbs(static function (Trail $trail) use ($coreService) {
                $form = Route::current()->parameter('form');

                return $trail
                    ->parent('platform.index')
                    ->push(
                        trans('kelnik-form::admin.menu.title'),
                        route($coreService->getFullRouteName('form.list'))
                    )
                    ->push(
                        $form->title,
                        route($coreService->getFullRouteName('form.edit'), $form)
                    )
                    ->push(trans('kelnik-form::admin.menu.logs'));
            });

        Route::screen('form/{form:id}/log/view/{log:id}', LogViewScreen::class)
            ->name($coreService->getFullRouteName('form.log.view'))
            ->breadcrumbs(static function (Trail $trail) use ($coreService) {
                $form = Route::current()->parameter('form');

                return $trail
                    ->parent('platform.index')
                    ->push(
                        trans('kelnik-form::admin.menu.title'),
                        route($coreService->getFullRouteName('form.list'))
                    )
                    ->push(
                        $form->title,
                        route($coreService->getFullRouteName('form.edit'), $form)
                    )
                    ->push(trans('kelnik-form::admin.menu.logs'));
            });

        // Log full list
        Route::screen('form/logs/list', LogFullListScreen::class)
            ->name($coreService->getFullRouteName('form.logs.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-form::admin.menu.logs'))
            );

        Route::screen('form/logs/view/{log:id}', LogFullViewScreen::class)
            ->name($coreService->getFullRouteName('form.logs.view'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-form::admin.menu.logs'))
            );
    });
