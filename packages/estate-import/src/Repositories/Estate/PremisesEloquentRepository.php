<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\Premises;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesRepository;

final class PremisesEloquentRepository extends BaseEloquentRepository implements PremisesRepository
{
    protected string $modelNamespace = Premises::class;
    protected array $lazyCollectionFields = [
        'id',
        'image_list_id',
        'image_plan_id',
        'image_3d_id',
        'external_id',
        'hash'
    ];

    public function findByExternalIdOrNew(int|float|string $externalId): Premises
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
