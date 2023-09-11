<?php

declare(strict_types=1);

namespace Kelnik\Document\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Document\Models\Group;

final class GroupEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Group $group, public string $event)
    {
    }
}
