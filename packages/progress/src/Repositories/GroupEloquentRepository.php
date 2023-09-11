<?php

declare(strict_types=1);

namespace Kelnik\Progress\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Progress\Models\Group;
use Kelnik\Progress\Repositories\Contracts\GroupRepository;

final class GroupEloquentRepository implements GroupRepository
{
    private string $model = Group::class;

    public function getAdminList(): Collection
    {
        return $this->model::orderBy('title')->get();
    }

    public function getActive(): Collection
    {
        return $this->model::active()->orderBy('title')->get();
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
