<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Services\Contracts;

interface VisualService
{
    public function getPremisesOnFloorPlan(int|string $primaryKey): array;

    public function getAssets(): array;
}
