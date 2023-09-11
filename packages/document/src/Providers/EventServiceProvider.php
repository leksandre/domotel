<?php

declare(strict_types=1);

namespace Kelnik\Document\Providers;

use Kelnik\Document\Events\CategoryEvent;
use Kelnik\Document\Events\ElementEvent;
use Kelnik\Document\Events\GroupEvent;
use Kelnik\Document\Listeners\ResetCacheByTag;
use Kelnik\Document\Listeners\ResetGroupCache;
use Kelnik\Document\Models\Category;
use Kelnik\Document\Models\Element;
use Kelnik\Document\Models\Group;
use Kelnik\Document\Observers\CategoryObserver;
use Kelnik\Document\Observers\ElementObserver;
use Kelnik\Document\Observers\GroupObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        CategoryEvent::class => [
            ResetCacheByTag::class
        ],
        ElementEvent::class => [
            ResetCacheByTag::class
        ],
        GroupEvent::class => [
            ResetGroupCache::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();

        Category::observe(CategoryObserver::class);
        Element::observe(ElementObserver::class);
        Group::observe(GroupObserver::class);
    }
}
