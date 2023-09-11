<?php

declare(strict_types=1);

namespace Kelnik\Contact\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseRepository
{
    public function findByPrimary(int|string $primary): Model;

    public function getActive(): Collection;

    public function save(Model $model): bool;

    public function delete(Model $model): bool;
}
