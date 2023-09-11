<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Progress\Http\Controllers\AlbumsPopupController;
use Kelnik\Progress\Http\Controllers\CamerasPopupController;

// API routes
//
Route::domain(config('platform.domain'))
    ->prefix('api/progress')
    ->name('kelnik.progress.')
    ->middleware(['api'])
    ->group(function () {
        Route::get('/albums/{group?}', AlbumsPopupController::class)->name('albums');
        Route::get('/cameras/{group?}', CamerasPopupController::class)->name('cameras');
    });
