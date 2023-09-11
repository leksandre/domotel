<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts\Estate;

use Illuminate\Support\Collection;

interface BaseRepository
{
    public function getForAdminByComplexPrimary(int|string $complexPrimary): Collection;
}
