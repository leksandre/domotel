<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts\Estate;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Section;

interface SectionRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Section;

    public function getParent(iterable $primaryKeys): Collection;
}
