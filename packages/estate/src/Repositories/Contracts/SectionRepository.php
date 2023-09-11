<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Section;

interface SectionRepository extends BaseRepository, AdminFilterBySelection
{
    public function findByPrimary(int|string $primary): Section;

    public function getAdminList(): LengthAwarePaginator;

    public function getAllByBuilding(int|string $buildingPrimary): Collection;
}
