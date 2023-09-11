<?php

declare(strict_types=1);

namespace Kelnik\Page\Http\Middleware\Contracts;

use Closure;
use Illuminate\Http\Request;

interface PageComponentMiddleware
{
    public function handle(Request $request, Closure $next, int $pageId, int $pageComponentId);
}
