<?php

declare(strict_types=1);

namespace Kelnik\Document\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Document\Models\Element;

final class ElementEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Element $element, public string $methodName)
    {
    }
}
