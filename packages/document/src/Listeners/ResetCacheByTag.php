<?php

declare(strict_types=1);

namespace Kelnik\Document\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Document\Events\CategoryEvent;
use Kelnik\Document\Events\ElementEvent;
use Kelnik\Document\Services\Contracts\DocumentService;

final class ResetCacheByTag implements ShouldQueue
{
    public function handle(CategoryEvent|ElementEvent $event): void
    {
        Cache::tags(resolve(DocumentService::class)->getCacheTag())->flush();
    }

    public function uniqueId(): string
    {
        return 'document';
    }
}
