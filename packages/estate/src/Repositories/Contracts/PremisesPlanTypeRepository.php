<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Kelnik\Estate\Models\PremisesPlanType;

interface PremisesPlanTypeRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): PremisesPlanType;

    public function isUnique(PremisesPlanType $premisesPlanType): bool;
}
