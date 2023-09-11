<?php

declare(strict_types=1);

namespace Kelnik\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Kelnik\Core\Http\Controllers\Traits\ApiResponse;

abstract class BaseApiController extends Controller
{
    use ApiResponse;
}
