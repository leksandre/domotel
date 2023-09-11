<?php

declare(strict_types=1);

namespace Kelnik\Form\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Form\Models\Log;
use Kelnik\Form\Repositories\Contracts\FormLogRepository;

final class FormLogEloquentRepository implements FormLogRepository
{
    /** @var class-string $model */
    private $model = Log::class;

    public function findByPrimary(int|string $primary): Log
    {
        return $this->model::findOrNew($primary);
    }

    public function getAllByFormPrimary(int|string $primary): Collection
    {
        return $this->model::query()->where('form_id', $primary)->latest()->get();
    }

    public function getAll(): Collection
    {
        return $this->model::query()->latest()->get();
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
