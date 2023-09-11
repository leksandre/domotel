<?php

declare(strict_types=1);

namespace Kelnik\Progress\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Kelnik\Core\Http\Controllers\BaseApiController;

abstract class ProgressController extends BaseApiController
{
    protected function getGroupId(): ?int
    {
        return (int)Route::current()->parameter('group') ?: null;
    }
}
