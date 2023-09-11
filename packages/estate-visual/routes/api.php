<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\EstateVisual\Http\Controllers\FrameSelectorController;
use Kelnik\EstateVisual\Http\Controllers\SelectorController;

// API routes
//
Route::domain(config('platform.domain'))
    ->prefix('api/estate-visual')
    ->name('kelnik.estateVisual.')
    ->middleware(['api'])
    ->group(function () {
        Route::get('/frame/{id}/{cid}', FrameSelectorController::class)
//            ->middleware(['throttle:40,1'])
            ->name('frame')
            ->where(['id' => '[0-9]+', 'cid' => '[0-9]+']);

        Route::post('/{id}/{cid}', SelectorController::class)
//            ->middleware(['throttle:40,1'])
            ->name('getData')
            ->where(['id' => '[0-9]+', 'cid' => '[0-9]+']);
    });
