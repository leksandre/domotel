<?php

declare(strict_types=1);

namespace Kelnik\Page\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Events\PageEvent;
use Kelnik\Page\Services\Contracts\PageService;

final class ResetPageCache implements ShouldQueue
{
    public function handle(PageEvent $event): void
    {
        $isDeleted = in_array($event->method, [$event::DELETED, $event::FORCE_DELETED]);
        $isUpdated = $event->method === $event::UPDATED
            && array_intersect(
                ['active', 'slug', 'path', 'meta', 'css_classes', 'redirect_type', 'redirect_url'],
                array_keys($event->page->getDirty())
            );

        if (!$isDeleted && !$isUpdated) {
            return;
        }

        /** @var PageService $pageService */
        $pageService = resolve(PageService::class);
        Cache::forget($pageService->getPageActiveComponentsCacheKey($event->page->getKey()));
        Cache::forget($pageService->getPageCacheKey($event->page->getKey(), $event->page->site_id));
        Cache::forget($pageService->getPageCacheTag($event->page->getKey()));

        $routeName = $pageService->getDynamicPageRouteNameById($event->page->getKey());

        if ($routeName) {
            Cache::tags($pageService->getDynComponentCacheTag($routeName))->flush();
        }
        unset($routeName);
    }
}
