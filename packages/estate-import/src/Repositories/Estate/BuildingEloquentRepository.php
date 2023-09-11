<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\Building;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BuildingRepository;

final class BuildingEloquentRepository extends BaseEloquentRepository implements BuildingRepository
{
    protected string $modelNamespace = Building::class;

    public function findByExternalIdOrNew(int|float|string $externalId): Building
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
