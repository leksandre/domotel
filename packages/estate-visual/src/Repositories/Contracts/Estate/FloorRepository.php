<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts\Estate;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Floor;

interface FloorRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Floor;

    public function getParent(iterable $primaryKeys): Collection;
}
