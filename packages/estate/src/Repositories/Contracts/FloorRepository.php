<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Floor;

interface FloorRepository extends BaseRepository, AdminFilterBySelection
{
    public function findByPrimary(int|string $primary): Floor;

    public function getAdminList(): LengthAwarePaginator;

    public function getAllByBuilding(int|string $buildingPrimary): Collection;

    public function getAllBySection(int|string $sectionPrimary): Collection;
}
