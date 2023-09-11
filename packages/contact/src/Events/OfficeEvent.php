<?php

declare(strict_types=1);

namespace Kelnik\Contact\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Contact\Models\Office;
use Kelnik\Core\Events\Contracts\ModelEvent;

final class OfficeEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Office $office, public string $event)
    {
    }
}
