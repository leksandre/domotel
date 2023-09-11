<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts;

use Kelnik\EstateVisual\Models\StepElementAnglePointer;

interface StepElementAnglePointerRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): StepElementAnglePointer;
}
