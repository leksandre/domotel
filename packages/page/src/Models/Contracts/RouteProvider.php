<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface RouteProvider
{
    public function makeRoutesByParams(array $params): Collection;

    public function validateSavingRequest(Request $request): void;

    public function getPrefixFromPath(string $path): string;
}
