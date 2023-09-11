<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesTypeGroupRepository;

final class PremisesTypeGroupEloquentRepository extends BaseEloquentRepository implements PremisesTypeGroupRepository
{
    protected string $modelNamespace = PremisesTypeGroup::class;
    protected array $lazyCollectionFields = ['id', 'external_id'];

    public function findByExternalIdOrNew(int|float|string $externalId): PremisesTypeGroup
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
