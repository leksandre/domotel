<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Mortgage\Platform\Screens\BankEditScreen;
use Kelnik\Mortgage\Platform\Screens\BankListScreen;
use Kelnik\Mortgage\Providers\MortgageServiceProvider;
use Tabuna\Breadcrumbs\Trail;

// Admin section
//
Route::domain(config('platform.domain'))
    ->prefix(config('platform.prefix'))
    ->middleware(['web', 'platform', 'platform.access:' . MortgageServiceProvider::MODULE_PERMISSION])
    ->group(function () {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        Route::screen('mortgage/bank/list', BankListScreen::class)
            ->name($coreService->getFullRouteName('mortgage.banks'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail
                    ->parent('platform.index')
                    ->push(trans('kelnik-mortgage::admin.menu.title'))
            );

        Route::screen('mortgage/bank/edit/{bank?}', BankEditScreen::class)
            ->name($coreService->getFullRouteName('mortgage.bank'))
            ->breadcrumbs(
                static fn(Trail $trail) => $trail
                    ->parent('platform.index')
                    ->push(trans('kelnik-mortgage::admin.menu.title'))
                    ->push(trans('kelnik-mortgage::admin.menu.banks'))
            );

        Route::patch('mortgage/bank/sort', [BankListScreen::class, 'sortable'])
            ->name($coreService->getFullRouteName('mortgage.banks.sort'));
    });
