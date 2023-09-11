<?php

declare(strict_types=1);

namespace Kelnik\Page\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Core\Events\SiteEvent;
use Kelnik\Page\Events\PageComponentEvent;
use Kelnik\Page\Events\PageComponentRouteEvent;
use Kelnik\Page\Events\PageEvent;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageService;

final class ResetRouteCache implements ShouldQueue, ShouldBeUnique
{
    private ModelEvent $event;

    public function handle(ModelEvent $event): void
    {
        $this->event = $event;

        // PageComponentRoute events
        if (
            $this->isPageComponentRouteEvent()
            && (
                $this->event->method === $this->event::UPDATED
                && !array_intersect(
                    ['ignore_page_slug', 'path', 'name', 'params'],
                    array_keys($this->event->pageComponentRoute->getDirty())
                )
            )
        ) {
            return;
        }

        // PageComponent events
        //
        if (
            $this->isPageComponentEvent()
            && (
                !$this->event->pageComponent
                || !$this->event->pageComponent->isDynamic()
                || (
                    $this->event->method === $this->event::UPDATED
                    && !in_array('active', array_keys($this->event->pageComponent->getDirty()))
                )
            )
        ) {
            return;
        }

        // Page events
        //
        if ($this->isPageEvent() && $this->event->method !== $event::DELETED) {
            $hasActiveDynComponent = $this->event->page->activeComponents->first(
                static fn(PageComponent $component) => $component->isDynamic()
            );

            $modified = $this->event->method === $event::UPDATED
                || array_intersect(['active', 'slug', 'path'], array_keys($this->event->page->getDirty()));

            if (!$hasActiveDynComponent && !$modified) {
                return;
            }
        }

        /** @var PageService $pageService */
        $pageService = resolve(PageService::class);
        Cache::forget($pageService::PAGE_ROUTES_CACHE);

        // Restore page routes cache
        //
        $pageService->loadPageRoutes();

        if (app()->routesAreCached()) {
            Artisan::call('route:cache');
        }
    }

    public function uniqueId()
    {
        if ($this->isPageComponentRouteEvent()) {
            return $this->event->pageComponentRoute->getKey();
        }

        if ($this->isPageComponentEvent()) {
            return $this->event->pageComponent->getKey();
        }

        return $this->event->page->getKey();
    }

    private function isPageEvent(): bool
    {
        return $this->event instanceof PageEvent;
    }

    private function isPageComponentEvent(): bool
    {
        return $this->event instanceof PageComponentEvent;
    }

    private function isPageComponentRouteEvent(): bool
    {
        return $this->event instanceof PageComponentRouteEvent;
    }

    private function isSiteEvent(): bool
    {
        return $this->event instanceof SiteEvent;
    }
}
