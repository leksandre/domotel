<?php

declare(strict_types=1);

namespace Kelnik\Estate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kelnik\Estate\Http\Controllers\PrintToPdf;
use Kelnik\Estate\View\Components\PremisesCard\RouteProvider;
use Kelnik\Page\Http\Middleware\Contracts\PageComponentMiddleware;

final class RedirectToPdfController implements PageComponentMiddleware
{
    public function handle(Request $request, Closure $next, int $pageId, int $pageComponentId)
    {
        if (Route::current()->parameter(RouteProvider::PARAM_PRINT) === RouteProvider::PRINT_TYPE_PDF) {
            return (new PrintToPdf())($request, $pageId, $pageComponentId);
        }

        return $next($request);
    }
}
