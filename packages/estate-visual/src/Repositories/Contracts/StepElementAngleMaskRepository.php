<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts;

use Kelnik\EstateVisual\Models\StepElementAngleMask;

interface StepElementAngleMaskRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): StepElementAngleMask;

    public function getPremisesOnFloorPlan(int|string $premisesPrimaryKey): StepElementAngleMask;
}
