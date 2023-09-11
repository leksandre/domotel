<?php

declare(strict_types=1);

namespace Kelnik\Progress\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Progress\Models\Group;

final class GroupEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Group $group, public string $event)
    {
    }
}
