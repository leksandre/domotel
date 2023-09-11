<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Listeners;

use Kelnik\EstateImport\Events\HistoryModelEvent;
use Kelnik\EstateImport\Jobs\RemoveHistoryData;

final class HistoryDeleted
{
    public function handle(HistoryModelEvent $event): void
    {
        if (
            !in_array($event->modelEvent, [$event::DELETED, $event::FORCE_DELETED])
            || !$event->modelData->pre_processor
        ) {
            return;
        }

        RemoveHistoryData::dispatch($event->modelData);
    }
}
