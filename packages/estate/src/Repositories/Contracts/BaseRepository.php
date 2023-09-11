<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;

interface BaseRepository
{
    public function findByPrimary(int|string $primary): EstateModel;

    public function getAll(array $fields = []): Collection;

    public function getByPrimary(iterable $primaryKeys): Collection;

    public function save(EstateModel $model): bool;

    public function saveQuietly(EstateModel $model): bool;

    public function delete(EstateModel $model): bool;
}
