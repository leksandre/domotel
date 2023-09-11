<?php

declare(strict_types=1);

namespace Kelnik\Page\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Page\Models\PageComponentRoute;

final class PageComponentRouteEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public PageComponentRoute $pageComponentRoute, public string $method)
    {
    }
}
