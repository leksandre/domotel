<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Providers;

use Kelnik\EstateVisual\Events\SelectorEvent;
use Kelnik\EstateVisual\Listeners\ResetSelectorCache;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Observers\SelectorObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        SelectorEvent::class => [
            ResetSelectorCache::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();

        Selector::observe(SelectorObserver::class);
    }
}
