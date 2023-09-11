<?php

declare(strict_types=1);

namespace Kelnik\Contact\Providers;

use Kelnik\Contact\Events\OfficeEvent;
use Kelnik\Contact\Events\SocialLinkEvent;
use Kelnik\Contact\Listeners\ResetOfficeCacheByTag;
use Kelnik\Contact\Listeners\ResetSocialCacheByTag;
use Kelnik\Contact\Models\Office;
use Kelnik\Contact\Models\SocialLink;
use Kelnik\Contact\Observers\OfficeObserver;
use Kelnik\Contact\Observers\SocialLinkObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        OfficeEvent::class => [
            ResetOfficeCacheByTag::class
        ],
        SocialLinkEvent::class => [
            ResetSocialCacheByTag::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();

        Office::observe(OfficeObserver::class);
        SocialLink::observe(SocialLinkObserver::class);
    }
}
