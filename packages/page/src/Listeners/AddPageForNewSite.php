<?php

declare(strict_types=1);

namespace Kelnik\Page\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kelnik\Core\Events\SiteEvent;
use Kelnik\Page\Database\Seeders\PageErrorSeeder;
use Kelnik\Page\Database\Seeders\PageSeeder;

final class AddPageForNewSite implements ShouldQueue, ShouldBeUnique
{
    public function handle(SiteEvent $event): void
    {
        if ($event->event !== $event::CREATED) {
            return;
        }

        (new PageSeeder($event->site))->run();
        (new PageErrorSeeder($event->site))->run();
    }
}
