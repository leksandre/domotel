<?php

declare(strict_types=1);

namespace Kelnik\Progress\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface BaseRepository
{
    public function getAdminList(): Collection|LengthAwarePaginator|LazyCollection;

    public function findByPrimary(int|string $primary): Model;

    public function save(Model $model): bool;

    public function delete(Model $model): bool;
}
