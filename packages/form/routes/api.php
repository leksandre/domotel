<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Form\Http\Controllers\FormController;

// API routes
//
Route::domain(config('platform.domain'))
    ->prefix('api/form')
    ->name('kelnik.form.')
    ->middleware(['api'])
    ->group(function () {
        Route::post('/submit/{id}', FormController::class)
            ->middleware(['throttle:20,1'])
            ->name('submit')
            ->where(['id' => '\d+']);
    });
