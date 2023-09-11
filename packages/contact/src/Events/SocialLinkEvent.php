<?php

declare(strict_types=1);

namespace Kelnik\Contact\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Contact\Models\SocialLink;
use Kelnik\Core\Events\Contracts\ModelEvent;

final class SocialLinkEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public SocialLink $socLink, public string $event)
    {
    }
}
