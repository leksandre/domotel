<?php

declare(strict_types=1);

namespace Kelnik\Page\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Events\PageComponentEvent;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;

final class ResetPageComponentCache implements ShouldQueue
{
    public function handle(PageComponentEvent $event): void
    {
        /** @var PageService $pageService */
        $pageService = resolve(PageService::class);
        Cache::forget($pageService->getPageActiveComponentsCacheKey($event->pageComponent->page_id));

        if (
            !in_array($event->method, [$event::UPDATED, $event::DELETED])
            || !$event->pageComponent?->exists
        ) {
            return;
        }

        $cacheTags = [
            $pageService->getPageComponentCacheTag($event->pageComponent->id)
        ];

        if ($event->pageComponent->component && $event->pageComponent->isDynamic()) {
            $route = $event->pageComponent->routes?->first();
            $routeName = $route ? resolve(PageLinkService::class)->getPageComponentRouteName($route) : null;

            if ($routeName) {
                $cacheTags[] = $pageService->getDynComponentCacheTag($routeName);
            }
            unset($routeName, $route);
        }

        Cache::tags($cacheTags)->flush();
        unset($cacheTags);
    }
}
