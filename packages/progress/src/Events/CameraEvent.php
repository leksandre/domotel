<?php

declare(strict_types=1);

namespace Kelnik\Progress\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Progress\Models\Camera;

final class CameraEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public Camera $camera, public string $event)
    {
    }
}
