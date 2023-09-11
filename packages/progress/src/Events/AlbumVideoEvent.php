<?php

declare(strict_types=1);

namespace Kelnik\Progress\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Progress\Models\AlbumVideo;

final class AlbumVideoEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public AlbumVideo $albumVideo, public string $event)
    {
    }
}
