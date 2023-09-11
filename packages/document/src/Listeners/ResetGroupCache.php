<?php

declare(strict_types=1);

namespace Kelnik\Document\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Document\Events\GroupEvent;
use Kelnik\Document\Services\Contracts\DocumentService;

final class ResetGroupCache implements ShouldQueue, ShouldBeUnique
{
    private GroupEvent $event;

    public function handle(GroupEvent $event): void
    {
        $this->event = $event;

        $this->handleEvent();
    }

    private function handleEvent(): void
    {
        if ($this->event !== $this->event::CREATED) {
            Cache::tags(
                resolve(DocumentService::class)->getGroupCacheTag($this->event->group->getKey())
            )->flush();
        }
    }
}
