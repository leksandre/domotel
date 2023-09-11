<?php

declare(strict_types=1);

namespace Kelnik\Progress\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Progress\Events\AlbumEvent;
use Kelnik\Progress\Events\AlbumVideoEvent;
use Kelnik\Progress\Services\Contracts\ProgressService;

final class ResetAlbumCache implements ShouldQueue, ShouldBeUnique
{
    private AlbumEvent|AlbumVideoEvent $event;
    private ProgressService $progressService;

    public function __construct()
    {
        $this->progressService = resolve(ProgressService::class);
    }

    public function handle(AlbumEvent|AlbumVideoEvent $event): void
    {
        $this->event = $event;

        $this->handleEvent();
    }

    private function handleEvent(): void
    {
        if ($this->isAlbum() && $this->event === $this->event::CREATED) {
            Cache::tags($this->progressService->getAlbumListCacheTag())->flush();

            return;
        }

        Cache::tags([
            $this->progressService->getAlbumCacheTag($this->getAlbumId()),
            $this->progressService->getAlbumListCacheTag()
        ])->flush();
    }

    private function isAlbum(): bool
    {
        return $this->event instanceof AlbumEvent;
    }

    private function getAlbumId(): int
    {
        return $this->isAlbum()
            ? $this->event->album->getKey()
            : $this->event->albumVideo->album_id;
    }

    public function uniqueId(): int
    {
        return $this->getAlbumId();
    }
}
