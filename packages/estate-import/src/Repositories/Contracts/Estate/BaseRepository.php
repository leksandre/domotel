<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Contracts\Estate;

use Illuminate\Database\Eloquent\Relations\Relation;
use Kelnik\Estate\Models\Contracts\EstateModel;

interface BaseRepository
{
    public function findByExternalIdOrNew(int|float|string $externalId): EstateModel;

    public function save(EstateModel $model): bool;

    public function saveQuietly(EstateModel $model): bool;

    public function removeRelation(Relation $relation): mixed;
}
