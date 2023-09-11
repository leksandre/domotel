<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Mortgage\Models\Bank;

final class BankEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Bank $bank, public string $event)
    {
    }
}
