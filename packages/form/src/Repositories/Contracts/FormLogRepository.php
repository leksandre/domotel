<?php

declare(strict_types=1);

namespace Kelnik\Form\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Form\Models\Log;

interface FormLogRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Log;

    public function getAllByFormPrimary(int|string $primary): Collection;

    public function getAll(): Collection;

    public function save(Model $model): bool;
}
