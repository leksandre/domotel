<?php

declare(strict_types=1);

namespace Kelnik\Page\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Page\Models\Page;

final class PageEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Page $page, public string $method)
    {
    }
}
