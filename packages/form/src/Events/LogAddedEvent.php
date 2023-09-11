<?php

declare(strict_types=1);

namespace Kelnik\Form\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Form\Models\Log;

final class LogAddedEvent
{
    use Dispatchable;

    public function __construct(public readonly Log $formLog)
    {
    }
}
