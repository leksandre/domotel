<?php

declare(strict_types=1);

namespace Kelnik\Menu\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Menu\Models\MenuItem;

final class MenuItemEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public readonly MenuItem $menuItem, public string $methodName)
    {
    }
}
