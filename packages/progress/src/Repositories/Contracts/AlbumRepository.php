<?php

declare(strict_types=1);

namespace Kelnik\Progress\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Progress\Models\Album;

interface AlbumRepository extends BaseRepository
{
    public function getAdminList(): LengthAwarePaginator;

    public function findByPrimaryWithImagesAndVideos(int|string $primary): Album;

    public function getActive(?int $group = null): Collection;

    public function save(Model $model, array $imageIds = [], ?array $videos = null): bool;
}
