<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\PremisesType;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesTypeRepository;

final class PremisesTypeEloquentRepository extends BaseEloquentRepository implements PremisesTypeRepository
{
    protected string $modelNamespace = PremisesType::class;
    protected array $lazyCollectionFields = ['id', 'replace_id', 'external_id'];

    public function findByExternalIdOrNew(int|float|string $externalId): PremisesType
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
