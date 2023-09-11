<?php

declare(strict_types=1);

namespace Kelnik\Progress\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Progress\Events\CameraEvent;
use Kelnik\Progress\Models\Camera;
use Kelnik\Progress\Observers\Contracts\Observer;

final class CameraObserver extends Observer
{
    /**
     * @param Camera $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        CameraEvent::dispatch($model, $event);
    }
}
