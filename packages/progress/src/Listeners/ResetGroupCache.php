<?php

declare(strict_types=1);

namespace Kelnik\Progress\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Progress\Events\GroupEvent;
use Kelnik\Progress\Services\Contracts\ProgressService;

final class ResetGroupCache implements ShouldQueue, ShouldBeUnique
{
    private GroupEvent $event;
    private ProgressService $progressService;

    public function __construct()
    {
        $this->progressService = resolve(ProgressService::class);
    }

    public function handle(GroupEvent $event): void
    {
        $this->event = $event;

        $this->handleEvent();
    }

    private function handleEvent(): void
    {
        if ($this->event !== $this->event::CREATED) {
            Cache::tags(
                $this->progressService->getGroupCacheTag($this->event->group->getKey())
            )->flush();
        }
    }
}
