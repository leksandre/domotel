<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kelnik\Image\Http\Controllers\ImageController;

// Admin section
//
//Route::domain(config('platform.domain'))
//    ->prefix(config('platform.prefix'))
//    ->middleware(['web', 'platform'])
//    ->group(function () {
//        ...
//    });

// Public
//
Route::domain(config('kelnik-image.route.domain'))
    ->prefix(config('kelnik-image.route.prefix'))
    ->middleware('web')
    ->get('{width}{height}{crop}{blur}{filename}', ImageController::class)
        ->where('width', '(w[\d]+/)?')
        ->where('height', '(h[\d]+/)?')
        ->where('crop', '(c/)?')
        ->where('blur', '(b/)?')
        ->where('filename', '[0-9a-zA-Z_\.\-]+')
        ->name(config('kelnik-image.route.name'));
