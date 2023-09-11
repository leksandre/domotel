<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BankRepository extends BaseRepository
{
    public function getAll(): Collection;

    public function getMaxPriority(): int;

    public function getActiveWithPrograms(array $bankIds = []): Collection;

    public function save(Model $model, ?array $newPrograms = null): bool;
}
