<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Estate;

use Kelnik\Estate\Models\Section;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\SectionRepository;

final class SectionEloquentRepository extends BaseEloquentRepository implements SectionRepository
{
    protected string $modelNamespace = Section::class;

    public function findByExternalIdOrNew(int|float|string $externalId): Section
    {
        return parent::findByExternalIdOrNew($externalId);
    }
}
