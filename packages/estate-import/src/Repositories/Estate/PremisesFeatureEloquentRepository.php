<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesFeatureRepository;

final class PremisesFeatureEloquentRepository extends BaseEloquentRepository implements PremisesFeatureRepository
{
    protected string $modelNamespace = PremisesFeature::class;
    protected array $lazyCollectionFields = ['id', 'group_id', 'external_id'];

    public function findByExternalIdOrNew(int|float|string $externalId): PremisesFeature
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
