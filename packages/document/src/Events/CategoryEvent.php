<?php

declare(strict_types=1);

namespace Kelnik\Document\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Document\Models\Category;

final class CategoryEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Category $category, public string $event)
    {
    }
}
