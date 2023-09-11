<?php

declare(strict_types=1);

namespace Kelnik\Estate\Listeners;

use Kelnik\Estate\Events\PlanoplanEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\Estate\Jobs\LoadPlanoplanData;

final class PlanoplanCreated implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PlanoplanEvent $event): void
    {
        if ($event->modelEvent === $event::CREATED) {
            LoadPlanoplanData::dispatch(...['planoplan' => $event->modelData]);
        }
    }
}
