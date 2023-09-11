<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\FBlock\Events\BlockEvent;
use Kelnik\FBlock\Services\Contracts\BlockService;

final class ResetBlockCache implements ShouldQueue
{
    public function handle(BlockEvent $event): void
    {
        Cache::tags(resolve(BlockService::class)->getCacheTag())->flush();
    }
}
