<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Building;

interface BuildingRepository extends BaseRepository, AdminFilterBySelection
{
    public function findByPrimary(int|string $primary): Building;

    public function getAdminList(): LengthAwarePaginator;

    public function getAllByComplex(int|string $complexPrimary): Collection;
}
