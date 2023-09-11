<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\EstateVisual\Models\Selector;

final class SelectorEvent extends ModelEvent
{
    use Dispatchable;

    public Selector $modelData;
    public string $modelEvent;

    public function __construct(Selector $model, string $modelEvent)
    {
        $this->modelData = $model;
        $this->modelEvent = $modelEvent;
    }
}
