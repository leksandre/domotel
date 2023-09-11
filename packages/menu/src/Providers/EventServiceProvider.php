<?php

declare(strict_types=1);

namespace Kelnik\Menu\Providers;

use Kelnik\Menu\Events\MenuEvent;
use Kelnik\Menu\Events\MenuItemEvent;
use Kelnik\Menu\Listeners\ResetMenuCache;
use Kelnik\Menu\Listeners\ResetMenuCacheByPage;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Models\MenuItem;
use Kelnik\Menu\Observers\MenuItemObserver;
use Kelnik\Menu\Observers\MenuObserver;
use Kelnik\Page\Events\PageComponentEvent;
use Kelnik\Page\Events\PageEvent;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        MenuEvent::class => [
            ResetMenuCache::class
        ],
        MenuItemEvent::class => [
            ResetMenuCache::class
        ],
        PageEvent::class => [
            ResetMenuCacheByPage::class
        ],
        PageComponentEvent::class => [
            ResetMenuCacheByPage::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();

        Menu::observe(MenuObserver::class);
        MenuItem::observe(MenuItemObserver::class);
    }
}
