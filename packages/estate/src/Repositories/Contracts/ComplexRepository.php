<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Kelnik\Estate\Models\Complex;

interface ComplexRepository extends BaseRepository, AdminFilterBySelection
{
    public function findByPrimary(int|string $primary): Complex;
}
