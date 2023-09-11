<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\Floor;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\FloorRepository;

final class FloorEloquentRepository extends BaseEloquentRepository implements FloorRepository
{
    protected string $modelNamespace = Floor::class;

    public function findByExternalIdOrNew(int|float|string $externalId): Floor
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
