<?php

declare(strict_types=1);

namespace Kelnik\Estate\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Events\PlanoplanEvent;
use Kelnik\Estate\Services\Contracts\PlanoplanService;

final class ResetPlanoplanCache implements ShouldQueue
{
    public function handle(PlanoplanEvent $event): void
    {
        if ($event->modelEvent === $event::CREATED) {
            return;
        }

        Cache::tags([
            resolve(PlanoplanService::class)->getCacheTag($event->modelData->getKey())
        ])->flush();
    }
}
