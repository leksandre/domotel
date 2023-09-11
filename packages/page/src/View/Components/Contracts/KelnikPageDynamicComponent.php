<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Contracts;

use Kelnik\Page\Models\Contracts\RouteProvider;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;

interface KelnikPageDynamicComponent
{
    public static function initRouteProvider(Page $page, PageComponent $pageComponent): RouteProvider;
}
