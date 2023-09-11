<?php

declare(strict_types=1);

namespace Kelnik\Progress\Services;

use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Video\Factory;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Progress\Dto\ElementSortDto;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\Camera;
use Kelnik\Progress\Repositories\Contracts\AlbumRepository;
use Kelnik\Progress\Repositories\Contracts\CameraRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;

final class ProgressService implements Contracts\ProgressService
{
    public function __construct(
        private AlbumRepository $albumRepository,
        private CameraRepository $cameraRepository,
        private CoreService $coreService
    ) {
    }

    public function getAlbums(?int $group = null): Collection
    {
        $res = $this->albumRepository->getActive($group);

        if ($res->isEmpty()) {
            return $res;
        }

        $hasImageModule = $this->coreService->hasModule('image');

        $res->each(static function (Album $album) use ($hasImageModule) {
            if ($album->images->isNotEmpty()) {
                $firstImage = $album->images->first();
                $album->coverImage = $firstImage->url();

                if ($hasImageModule) {
                    $album->coverPicture = Picture::init(new ImageFile($firstImage))
                        ->setLazyLoad(true)
                        ->setBreakpoints([1280 => 360, 960 => 516, 670 => 387, 320 => 558])
                        ->setImageAttribute('alt', $firstImage->alt ?? $album->title ?? '')
                        ->render();
                }

                return;
            }

            if ($album->videos->isNotEmpty()) {
                $album->coverImage = $album->videos->first()->url
                    ? Factory::make($album->videos->first()->url)?->getThumb()
                    : null;
            }
        });

        return $res;
    }

    public function getCameras(?int $group = null): Collection
    {
        return $this->cameraRepository->getActive($group);
    }

    public function sortCameras(ElementSortDto $dto): bool
    {
        $elements = $this->cameraRepository->getAll();

        if ($elements->isEmpty()) {
            return false;
        }

        $elements->each(function (Camera $el) use ($dto) {
            $el->priority = (int)array_search($el->getKey(), $dto->elements) + $dto->defaultPriority;
            $this->cameraRepository->save($el);
        });

        return true;
    }

    public function getContentCamerasLink(): Field
    {
        return Link::make(trans('kelnik-progress::admin.contentLink'))
            ->route($this->coreService->getFullRouteName('progress.cameras'))
            ->icon('bs.database')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px')
            ->hr();
    }

    public function getContentAlbumsLink(): Field
    {
        return Link::make(trans('kelnik-progress::admin.contentLink'))
            ->route($this->coreService->getFullRouteName('progress.albums'))
            ->icon('bs.database')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px');
    }

    public function getContentGroupsLink(): Field
    {
        return Link::make(trans('kelnik-progress::admin.contentLink'))
            ->route($this->coreService->getFullRouteName('progress.groups'))
            ->icon('bs.database')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px')
            ->hr();
    }

    public function getAlbumCacheTag(int|string $id): string
    {
        return 'progressAlbum_' . $id;
    }

    public function getAlbumListCacheTag(): string
    {
        return 'progressAlbumList';
    }

    public function getCameraCacheTag(int|string $id): string
    {
        return 'progressCamera_' . $id;
    }

    public function getCameraListCacheTag(): string
    {
        return 'progressCameraList';
    }

    public function getGroupCacheTag(int|string $id): string
    {
        return 'progressGroup_' . $id;
    }
}
