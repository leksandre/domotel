<?php

declare(strict_types=1);

namespace Kelnik\News\Providers;

use Kelnik\News\Events\CategoryEvent;
use Kelnik\News\Events\ElementEvent;
use Kelnik\News\Listeners\DeletePageLink;
use Kelnik\News\Listeners\ResetCategoryCache;
use Kelnik\News\Listeners\ResetElementCache;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Observers\CategoryObserver;
use Kelnik\News\Observers\ElementObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        CategoryEvent::class => [
            ResetCategoryCache::class,
            DeletePageLink::class
        ],
        ElementEvent::class => [
            ResetElementCache::class
        ]
    ];

    public function boot(): void
    {
        Category::observe(CategoryObserver::class);
        Element::observe(ElementObserver::class);
    }
}
