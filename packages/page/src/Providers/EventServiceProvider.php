<?php

declare(strict_types=1);

namespace Kelnik\Page\Providers;

use Kelnik\Core\Events\ModuleCleared;
use Kelnik\Core\Events\SiteEvent;
use Kelnik\Page\Events\PageComponentEvent;
use Kelnik\Page\Events\PageComponentRouteEvent;
use Kelnik\Page\Events\PageEvent;
use Kelnik\Page\Listeners\AddPageForNewSite;
use Kelnik\Page\Listeners\DeletePageComponentRouteByModule;
use Kelnik\Page\Listeners\DeletePages;
use Kelnik\Page\Listeners\ModifyPageTypeByComponent;
use Kelnik\Page\Listeners\ResetPageCache;
use Kelnik\Page\Listeners\ResetPageComponentCache;
use Kelnik\Page\Listeners\ResetPageComponentRouteCache;
use Kelnik\Page\Listeners\ResetRouteCache;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Observers\PageComponentObserver;
use Kelnik\Page\Observers\PageComponentRouteObserver;
use Kelnik\Page\Observers\PageObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        ModuleCleared::class => [
            DeletePageComponentRouteByModule::class
        ],
        PageComponentRouteEvent::class => [
            ResetPageComponentRouteCache::class,
            ResetRouteCache::class
        ],
        PageComponentEvent::class => [
            ResetPageComponentCache::class,
            ResetRouteCache::class,
            ModifyPageTypeByComponent::class
        ],
        PageEvent::class => [
            ResetPageCache::class,
            ResetRouteCache::class
        ],
        SiteEvent::class => [
            AddPageForNewSite::class,
            ResetRouteCache::class,
            DeletePages::class
        ]
    ];

    public function boot(): void
    {
        Page::observe(PageObserver::class);
        PageComponent::observe(PageComponentObserver::class);
        PageComponentRoute::observe(PageComponentRouteObserver::class);

        parent::boot();
    }
}
