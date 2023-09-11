<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Contact\Platform\Screens\OfficeEditScreen;
use Kelnik\Contact\Platform\Screens\OfficeListScreen;
use Kelnik\Contact\Platform\Screens\SocialEditScreen;
use Kelnik\Contact\Platform\Screens\SocialListScreen;
use Kelnik\Contact\Providers\ContactServiceProvider;
use Kelnik\Core\Services\Contracts\CoreService;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . ContactServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        // Offices
        Route::screen('contact/office/list', OfficeListScreen::class)
            ->name($coreService->getFullRouteName('contact.office.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-contact::admin.menu.title'))
            );

        Route::screen('contact/office/edit/{office?}', OfficeEditScreen::class)
            ->name($coreService->getFullRouteName('contact.office.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-contact::admin.menu.title'))
                    ->push(trans('kelnik-contact::admin.menu.offices'))
            );

        Route::patch('contact/office/sort', [OfficeListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('contact.office.sort'));

        // Social links
        Route::screen('contact/social/list', SocialListScreen::class)
            ->name($coreService->getFullRouteName('contact.social.list'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-contact::admin.menu.title'))
            );

        Route::screen('contact/social/edit/{social?}', SocialEditScreen::class)
            ->name($coreService->getFullRouteName('contact.social.edit'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail->parent('platform.index')
                    ->push(trans('kelnik-contact::admin.menu.title'))
                    ->push(trans('kelnik-contact::admin.menu.social'))
            );

        Route::patch('contact/social/sort', [SocialListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('contact.social.sort'));
    });
