<?php

declare(strict_types=1);

namespace Kelnik\Progress\Providers;

use Kelnik\Progress\Events\AlbumEvent;
use Kelnik\Progress\Events\AlbumVideoEvent;
use Kelnik\Progress\Events\CameraEvent;
use Kelnik\Progress\Events\GroupEvent;
use Kelnik\Progress\Listeners\ResetAlbumCache;
use Kelnik\Progress\Listeners\ResetCameraCache;
use Kelnik\Progress\Listeners\ResetGroupCache;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\AlbumVideo;
use Kelnik\Progress\Models\Camera;
use Kelnik\Progress\Models\Group;
use Kelnik\Progress\Observers\AlbumObserver;
use Kelnik\Progress\Observers\AlbumVideoObserver;
use Kelnik\Progress\Observers\CameraObserver;
use Kelnik\Progress\Observers\GroupObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        AlbumEvent::class => [
            ResetAlbumCache::class
        ],
        AlbumVideoEvent::class => [
            ResetAlbumCache::class
        ],
        CameraEvent::class => [
            ResetCameraCache::class
        ],
        GroupEvent::class => [
            ResetGroupCache::class
        ]
    ];

    public function boot(): void
    {
        Album::observe(AlbumObserver::class);
        AlbumVideo::observe(AlbumVideoObserver::class);
        Camera::observe(CameraObserver::class);
        Group::observe(GroupObserver::class);
    }
}
