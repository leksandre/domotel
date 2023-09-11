<?php

declare(strict_types=1);

namespace Kelnik\Estate\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Estate\Models\Planoplan;

final class PlanoplanEvent extends ModelEvent
{
    use Dispatchable;

    public Planoplan $modelData;
    public string $modelEvent;

    public function __construct(Planoplan $model, string $modelEvent)
    {
        $this->modelData = $model;
        $this->modelEvent = $modelEvent;
    }
}
