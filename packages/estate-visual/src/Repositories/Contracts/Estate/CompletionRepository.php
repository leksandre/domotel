<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts\Estate;

use Illuminate\Support\Collection;

interface CompletionRepository
{
    /**
     * @param int[]|string[] $buildingKeys
     * @return Collection
     */
    public function getByBuildingPrimaryKeys(array $buildingKeys): Collection;
}
