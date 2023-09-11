<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesStatusRepository;

final class PremisesStatusEloquentRepository extends BaseEloquentRepository implements PremisesStatusRepository
{
    protected string $modelNamespace = PremisesStatus::class;
    protected array $lazyCollectionFields = ['id', 'replace_id', 'external_id'];

    public function findByExternalIdOrNew(int|float|string $externalId): PremisesStatus
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
