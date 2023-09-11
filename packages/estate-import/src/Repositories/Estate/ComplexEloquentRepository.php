<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\Complex;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\ComplexRepository;

final class ComplexEloquentRepository extends BaseEloquentRepository implements ComplexRepository
{
    protected string $modelNamespace = Complex::class;

    public function findByExternalIdOrNew(int|float|string $externalId): Complex
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
