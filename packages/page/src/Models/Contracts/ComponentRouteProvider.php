<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Contracts;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;

abstract class ComponentRouteProvider implements RouteProvider
{
    /** PageComponentRoute collection*/
    protected Collection $routes;

    public function __construct(protected Page $page, protected PageComponent $pageComponent)
    {
        if (!$pageComponent->isDynamic()) {
            throw new InvalidArgumentException('Instance of `KelnikPageDynamicComponent` required');
        }

        $this->routes = collect();
    }
}
