<?php

declare(strict_types=1);

namespace Kelnik\Menu\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Menu\Events\MenuEvent;
use Kelnik\Menu\Events\MenuItemEvent;
use Kelnik\Menu\Services\Contracts\MenuService;

final class ResetMenuCache implements ShouldQueue, ShouldBeUnique
{
    private readonly MenuEvent|MenuItemEvent $event;
    private readonly MenuService $menuService;

    public function __construct()
    {
        $this->menuService = resolve(MenuService::class);
    }

    public function handle(MenuEvent|MenuItemEvent $event): void
    {
        $this->event = $event;

        if ($this->isMenuEvent()) {
            $this->handleMenuEvent();
            return;
        }

        $this->handleItemEvent();
    }

    private function handleMenuEvent(): void
    {
        Cache::tags([
            $this->menuService->getCacheTag($this->event->menu->getKey())
        ])->flush();
    }

    private function handleItemEvent(): void
    {
        Cache::tags([
            $this->menuService->getCacheTag($this->event->menuItem->menu_id)
        ])->flush();
    }

    private function isMenuEvent(): bool
    {
        return $this->event instanceof MenuEvent;
    }

    public function uniqueId(): int
    {
        return $this->isMenuEvent()
            ? $this->event->menu->getKey()
            : $this->event->menuItem->menu_id;
    }
}
