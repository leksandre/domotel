<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Mortgage\Models\Program;

final class ProgramEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Program $program, public string $methodName)
    {
    }
}
