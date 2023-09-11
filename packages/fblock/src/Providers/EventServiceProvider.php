<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Providers;

use Kelnik\FBlock\Events\BlockEvent;
use Kelnik\FBlock\Listeners\ResetBlockCache;
use Kelnik\FBlock\Models\FlatBlock;
use Kelnik\FBlock\Observers\BlockObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        BlockEvent::class => [
            ResetBlockCache::class
        ]
    ];

    public function boot(): void
    {
        FlatBlock::observe(BlockObserver::class);
    }
}
