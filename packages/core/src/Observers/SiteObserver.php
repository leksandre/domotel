<?php

declare(strict_types=1);

namespace Kelnik\Core\Observers;

use Kelnik\Core\Events\SiteEvent;
use Kelnik\Core\Models\Site;

final class SiteObserver
{
    public function created(Site $site): void
    {
        $this->handle($site, __FUNCTION__);
    }

    public function updated(Site $site): void
    {
        $this->handle($site, __FUNCTION__);
    }

    public function deleted(Site $site): void
    {
        $this->handle($site, __FUNCTION__);
    }

    public function restored(Site $site): void
    {
        $this->handle($site, __FUNCTION__);
    }

    public function forceDeleted(Site $site): void
    {
        $this->handle($site, __FUNCTION__);
    }

    private function handle(Site $site, string $event): void
    {
        SiteEvent::dispatch($site, $event);
    }
}
