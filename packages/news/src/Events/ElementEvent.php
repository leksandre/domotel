<?php

declare(strict_types=1);

namespace Kelnik\News\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\News\Models\Element;

final class ElementEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public readonly Element $element, public string $methodName)
    {
    }
}
