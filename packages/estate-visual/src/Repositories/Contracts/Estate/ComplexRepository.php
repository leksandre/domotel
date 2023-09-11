<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts\Estate;

use Kelnik\Estate\Models\Complex;

interface ComplexRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Complex;
}
