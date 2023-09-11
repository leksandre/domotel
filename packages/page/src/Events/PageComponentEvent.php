<?php

declare(strict_types=1);

namespace Kelnik\Page\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Page\Models\PageComponent;

final class PageComponentEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public PageComponent $pageComponent, public string $method)
    {
        if ($this->pageComponent->isDynamic()) {
            $this->pageComponent->routes()->get();
        }
    }
}
