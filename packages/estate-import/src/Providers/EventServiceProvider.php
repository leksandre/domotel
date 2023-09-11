<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Providers;

use Kelnik\EstateImport\Events\HistoryModelEvent;
use Kelnik\EstateImport\Listeners\HistoryDeleted;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Observers\HistoryObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        HistoryModelEvent::class => [
            HistoryDeleted::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();

        History::observe(HistoryObserver::class);
    }
}
