<?php

declare(strict_types=1);

namespace Kelnik\Core\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Events\SiteEvent;
use Kelnik\Core\Services\Contracts\SiteService;

final class ResetSiteCache implements ShouldQueue, ShouldBeUnique
{
    public function handle(SiteEvent $event): void
    {
        Cache::tags([SiteService::SITES_CACHE_TAG])->flush();
    }
}
