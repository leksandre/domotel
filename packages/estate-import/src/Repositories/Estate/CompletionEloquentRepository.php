<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Models\Complex;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\ComplexRepository;

final class CompletionEloquentRepository extends BaseEloquentRepository implements ComplexRepository
{
    protected string $modelNamespace = Completion::class;
    protected array $lazyCollectionFields = ['id', 'event_date', 'external_id'];

    public function findByExternalIdOrNew(int|float|string $externalId): Complex
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
