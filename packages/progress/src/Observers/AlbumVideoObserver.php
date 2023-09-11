<?php

declare(strict_types=1);

namespace Kelnik\Progress\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Progress\Events\AlbumVideoEvent;
use Kelnik\Progress\Models\AlbumVideo;
use Kelnik\Progress\Observers\Contracts\Observer;

final class AlbumVideoObserver extends Observer
{
    /**
     * @param AlbumVideo $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        AlbumVideoEvent::dispatch($model, $event);
    }
}
