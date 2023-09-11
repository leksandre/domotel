<?php

declare(strict_types=1);

namespace Kelnik\Progress\Services\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Progress\Dto\ElementSortDto;
use Kelnik\Progress\Repositories\Contracts\AlbumRepository;
use Kelnik\Progress\Repositories\Contracts\CameraRepository;
use Orchid\Screen\Field;

interface ProgressService
{
    public function __construct(
        AlbumRepository $albumRepository,
        CameraRepository $cameraRepository,
        CoreService $coreService
    );

    public function getAlbums(?int $group = null): Collection;

    public function getCameras(?int $group = null): Collection;

    public function sortCameras(ElementSortDto $dto): bool;

    public function getContentCamerasLink(): Field;

    public function getContentAlbumsLink(): Field;

    public function getContentGroupsLink(): Field;

    public function getAlbumCacheTag(int|string $id): string;

    public function getAlbumListCacheTag(): string;

    public function getCameraCacheTag(int|string $id): string;

    public function getCameraListCacheTag(): string;

    public function getGroupCacheTag(int|string $id): string;
}
