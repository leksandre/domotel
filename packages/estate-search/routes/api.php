<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\EstateSearch\Http\Controllers\SearchController;

// API routes
//
Route::domain(config('platform.domain'))
    ->prefix('api/estate-search')
    ->name('kelnik.estateSearch.')
    ->middleware(['api'])
    ->group(function () {
        Route::match(['get', 'post'], '/results/{cid}', SearchController::class)
            ->middleware(['throttle:40,1'])
            ->name('results')
            ->where(['cid' => '[0-9]+']);
    });
