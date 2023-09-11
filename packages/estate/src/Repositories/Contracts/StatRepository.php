<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;

interface StatRepository
{
    public function getMinPriceByModuleAndPremisesTypes(string $modelName, array $premisesTypes): Collection;

    public function getStatByTypes(array $typeIds): array;

    public function getObjectsStat(int|string $complexId, array $statuses, array $modelNames): array;

    public function getEdgePrices(array $modelNames): array;

    public function updateStat(array $models, array $data): void;
}
