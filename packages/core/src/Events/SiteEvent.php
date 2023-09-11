<?php

declare(strict_types=1);

namespace Kelnik\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Core\Models\Site;

final class SiteEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public readonly Site $site, public string $event)
    {
    }
}
