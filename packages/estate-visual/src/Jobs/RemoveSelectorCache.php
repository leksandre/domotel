<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Services\Contracts\SelectorService;

final class RemoveSelectorCache implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(private Selector $selector)
    {
    }

    public function handle(): void
    {
        Cache::tags([
            resolve(SelectorService::class)->getCacheTag($this->selector)
        ])->flush();
    }
}
