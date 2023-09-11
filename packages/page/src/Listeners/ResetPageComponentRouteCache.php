<?php

declare(strict_types=1);

namespace Kelnik\Page\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Events\PageComponentRouteEvent;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;

final class ResetPageComponentRouteCache implements ShouldQueue
{
    public function handle(PageComponentRouteEvent $event): void
    {
        if (!in_array($event->method, [$event::UPDATED, $event::DELETED])) {
            return;
        }

        $routeName = resolve(PageLinkService::class)->getPageComponentRouteName($event->pageComponentRoute);

        if (!$routeName) {
            return;
        }

        Cache::tags([resolve(PageService::class)->getDynComponentCacheTag($routeName)])->flush();
        unset($routeName);
    }
}
