<?php

declare(strict_types=1);

namespace Kelnik\Estate\Events;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Estate\Models\Contracts\EstateModel;

final class EstateModelEvent extends ModelEvent
{
    use Dispatchable;

    public EstateModel|Pivot $modelData;
    public string $modelEvent;

    public function __construct(EstateModel|Pivot $model, string $modelEvent)
    {
        $this->modelData = $model;
        $this->modelEvent = $modelEvent;
    }
}
