<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesFeatureGroupRepository;

final class PremisesFeatureGroupEloquentRepository extends BaseEloquentRepository implements
    PremisesFeatureGroupRepository
{
    protected string $modelNamespace = PremisesFeatureGroup::class;
    protected array $lazyCollectionFields = ['id', 'external_id'];

    public function findByExternalIdOrNew(int|float|string $externalId): PremisesFeatureGroup
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
