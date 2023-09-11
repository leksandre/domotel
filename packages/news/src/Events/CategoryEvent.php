<?php

declare(strict_types=1);

namespace Kelnik\News\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\News\Models\Category;

final class CategoryEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public readonly Category $category, public string $event)
    {
    }
}
