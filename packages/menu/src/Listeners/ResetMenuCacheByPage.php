<?php

declare(strict_types=1);

namespace Kelnik\Menu\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Menu\Models\MenuItem;
use Kelnik\Menu\Repositories\Contracts\MenuRepository;
use Kelnik\Menu\Services\Contracts\MenuService;
use Kelnik\Page\Events\PageComponentEvent;
use Kelnik\Page\Events\PageEvent;

final class ResetMenuCacheByPage implements ShouldQueue, ShouldBeUnique
{
    private readonly MenuService $menuService;
    private readonly MenuRepository $menuRepository;

    public function __construct()
    {
        $this->menuService = resolve(MenuService::class);
        $this->menuRepository = resolve(MenuRepository::class);
    }

    public function handle(PageEvent|PageComponentEvent $event): void
    {
        if ($event->method === $event::CREATED) {
            return;
        }

        $pageId = $pageComponentId = 0;

        if ($event instanceof PageEvent) {
            $pageId = $event->page->getKey();
        }

        if ($event instanceof PageComponentEvent) {
            $pageComponentId = $event->pageComponent->getKey();
        }

        if (!$pageId && !$pageComponentId) {
            return;
        }

        $this->resetCacheByPageOrPageComponent($pageId, $pageComponentId);
    }

    private function resetCacheByPageOrPageComponent(int|string $pageKey, int|string $pageComponentKey): bool
    {
        $menuItems = $this->menuRepository->findByPageOrPageComponent($pageKey, $pageComponentKey);

        if ($menuItems->isEmpty()) {
            return false;
        }

        $menuItems->each(fn(MenuItem $item) => Cache::tags([$this->menuService->getCacheTag($item->menu_id)])->flush());

        return true;
    }
}
