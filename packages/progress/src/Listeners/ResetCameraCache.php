<?php

declare(strict_types=1);

namespace Kelnik\Progress\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Progress\Events\CameraEvent;
use Kelnik\Progress\Services\Contracts\ProgressService;

final class ResetCameraCache implements ShouldQueue, ShouldBeUnique
{
    private CameraEvent $event;
    private ProgressService $progressService;

    public function __construct()
    {
        $this->progressService = resolve(ProgressService::class);
    }

    public function handle(CameraEvent $event): void
    {
        $this->event = $event;

        $this->handleEvent();
    }

    private function handleEvent(): void
    {
        if ($this->event === $this->event::CREATED) {
            Cache::tags($this->progressService->getCameraListCacheTag())->flush();

            return;
        }

        Cache::tags([
            $this->progressService->getCameraCacheTag($this->event->camera->getKey()),
            $this->progressService->getCameraListCacheTag()
        ])->flush();
    }

    public function uniqueId(): int
    {
        return $this->event->camera->getKey();
    }
}
