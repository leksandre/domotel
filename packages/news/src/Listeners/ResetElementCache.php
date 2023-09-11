<?php

declare(strict_types=1);

namespace Kelnik\News\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\News\Events\ElementEvent;
use Kelnik\News\Services\Contracts\NewsService;

final class ResetElementCache implements ShouldQueue
{
    public function __construct(private readonly NewsService $newsService)
    {
    }

    public function handle(ElementEvent $event): void
    {
        if ($event->methodName === $event::CREATED) {
            if ($event->element->category_id && $event->element->isActive()) {
                Cache::tags([$this->newsService->getCategoryCacheTag($event->element->category_id)])->flush();
            }

            return;
        }

        $tags = [
            $this->newsService->getElementCacheTag($event->element->id)
        ];

        if ($event->element->category_id) {
            $tags[] = $this->newsService->getCategoryCacheTag($event->element->category_id);
        }

        Cache::tags($tags)->flush();
    }
}
