<?php

declare(strict_types=1);

namespace Kelnik\Form\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Form\Models\Field;

interface FormFieldRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Field;

    public function getAll(): Collection;

    public function save(Model $model): bool;
}
