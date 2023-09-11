<?php

declare(strict_types=1);

namespace Kelnik\News\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\News\Events\CategoryEvent;
use Kelnik\News\Services\Contracts\NewsService;

final class ResetCategoryCache implements ShouldQueue
{
    public function __construct(private NewsService $newsService)
    {
    }

    public function handle(CategoryEvent $event): void
    {
        if ($event->event === $event::CREATED) {
            return;
        }

        Cache::tags($this->newsService->getCategoryCacheTag($event->category->id))->flush();
    }
}
