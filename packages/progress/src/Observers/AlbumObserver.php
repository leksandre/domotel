<?php

declare(strict_types=1);

namespace Kelnik\Progress\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Progress\Events\AlbumEvent;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Observers\Contracts\Observer;

final class AlbumObserver extends Observer
{
    /**
     * @param Album $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        AlbumEvent::dispatch($model, $event);
    }
}
