<?php

declare(strict_types=1);

namespace Kelnik\Progress\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Progress\Models\Camera;
use Kelnik\Progress\Repositories\Contracts\CameraRepository;

final class CameraEloquentRepository implements CameraRepository
{
    private string $model = Camera::class;

    public function getAll(): Collection
    {
        return $this->model::get();
    }

    public function getAdminList(): Collection
    {
        return $this->model::with('group')->orderBy('priority')->get();
    }

    public function getActive(?int $group = null): Collection
    {
        return $this->model::with('cover')
            ->when(
                $group,
                static fn(Builder $builder) => $builder->whereHas(
                    'group',
                    static fn(Builder $query) => $query->select('id')->active()->whereKey($group)
                )
            )
            ->active()
            ->orderBy('priority')
            ->get();
    }

    public function findByPrimary(int|string $primary): Model
    {
        return $this->model::findOrNew($primary);
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
