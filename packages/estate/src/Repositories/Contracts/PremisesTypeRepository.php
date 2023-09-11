<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesType;

interface PremisesTypeRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): PremisesType;

    public function isUnique(PremisesType $premisesType): bool;

    public function getByGroupPrimary(int|string $primary): Collection;
}
