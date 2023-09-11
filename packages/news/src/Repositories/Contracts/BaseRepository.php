<?php

declare(strict_types=1);

namespace Kelnik\News\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BaseRepository
{
    public function isUnique(Model $model): bool;

    public function findByPrimary(int|string $primary): Model;

    public function save(Model $model): bool;

    public function delete(Model $model): bool;
}
