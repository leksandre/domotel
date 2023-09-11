<?php

declare(strict_types=1);

namespace Kelnik\Core\Providers;

use Kelnik\Core\Events\SettingUpdated;
use Kelnik\Core\Events\SiteEvent;
use Kelnik\Core\Listeners\ResetSettingCache;
use Kelnik\Core\Listeners\ResetSiteCache;
use Kelnik\Core\Listeners\ResetThemeCache;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Observers\SiteObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        SettingUpdated::class => [
            ResetSettingCache::class,
            ResetThemeCache::class
        ],
        SiteEvent::class => [
            ResetSiteCache::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();

        Site::observe(SiteObserver::class);
    }
}
