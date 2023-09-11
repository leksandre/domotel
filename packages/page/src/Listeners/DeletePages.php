<?php

declare(strict_types=1);

namespace Kelnik\Page\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kelnik\Core\Events\SiteEvent;
use Kelnik\Page\Repositories\Contracts\PageRepository;

final class DeletePages implements ShouldQueue, ShouldBeUnique
{
    public function handle(SiteEvent $event): void
    {
        if (!in_array($event->event, [$event::DELETED, $event::FORCE_DELETED])) {
            return;
        }

        resolve(PageRepository::class)->findPrimaryPagesBySite($event->site->getKey())->each->delete();
    }
}
