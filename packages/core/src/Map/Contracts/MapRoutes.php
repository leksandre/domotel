<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Contracts;

use Illuminate\Support\Collection;

interface MapRoutes
{
    public function addMRoute(Route $route);

    public function getRoutes(): Collection;
}
