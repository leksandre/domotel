<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Repositories\Contracts\AdminBaseRepository;
use Kelnik\EstateVisual\Repositories\Contracts\BaseRepository;

abstract class BaseEloquentRepository implements BaseRepository, AdminBaseRepository
{
    protected string $modelNamespace;

    public function getAll(): Collection
    {
        return $this->modelNamespace::get();
    }

    public function getByPrimary(iterable $primaryKeys): Collection
    {
        return $this->modelNamespace::whereKey($primaryKeys)->get();
    }

    public function save(Model $model): bool
    {
        return $model->save();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    // Admin
    public function getAllForAdmin(): Collection
    {
        return $this->modelNamespace::adminList()->get();
    }

    public function getAllForAdminPaginated(): LengthAwarePaginator
    {
        return $this->modelNamespace::adminList()->paginate();
    }

    public function getByPrimaryForAdmin(iterable $primaryKeys): Collection
    {
        return $this->modelNamespace::whereKey($primaryKeys)->adminList()->get();
    }
}
