<?php

declare(strict_types=1);

namespace Kelnik\News\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\News\Events\CategoryEvent;
use Kelnik\Page\Services\Contracts\PageLinkService;

final class DeletePageLink implements ShouldQueue
{
    public function handle(CategoryEvent $event): void
    {
        if ($event->event !== $event::DELETED || !resolve(CoreService::class)->hasModule('page')) {
            return;
        }

        resolve(PageLinkService::class)->deletePageComponentRouteElements(
            $event->category::class,
            $event->category->getKey()
        );
    }
}
