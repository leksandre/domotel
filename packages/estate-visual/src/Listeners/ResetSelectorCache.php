<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Listeners;

use Kelnik\EstateVisual\Events\SelectorEvent;
use Kelnik\EstateVisual\Jobs\RemoveSelectorCache;

final class ResetSelectorCache
{
    public function handle(SelectorEvent $event): void
    {
        if (!in_array($event->modelEvent, [$event::DELETED, $event::FORCE_DELETED, $event::UPDATED])) {
            return;
        }

        RemoveSelectorCache::dispatch(...['selector' => $event->modelData]);
    }
}
