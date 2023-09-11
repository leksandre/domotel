<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\EstateImport\Models\History;

final class HistoryModelEvent extends ModelEvent
{
    use Dispatchable;

    public History $modelData;
    public string $modelEvent;

    public function __construct(History $model, string $modelEvent)
    {
        $this->modelData = $model;
        $this->modelEvent = $modelEvent;
    }
}
