<?php

declare(strict_types=1);

namespace Kelnik\Form\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Form\Models\Field;
use Kelnik\Form\Repositories\Contracts\FormFieldRepository;

final class FormFieldEloquentRepository implements FormFieldRepository
{
    /** @var class-string $model */
    private $model = Field::class;

    public function findByPrimary(int|string $primary): Field
    {
        return $this->model::findOrNew($primary);
    }

    public function getAll(): Collection
    {
        return $this->model::query()->orderBy('priority')->get();
    }

    public function save(Model $model): bool
    {
        return $model->save();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
