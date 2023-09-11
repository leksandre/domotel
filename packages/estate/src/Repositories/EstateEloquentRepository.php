<?php

namespace Kelnik\Estate\Repositories;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Repositories\Contracts\AdminBaseRepository;
use Kelnik\Estate\Repositories\Contracts\BaseRepository;

abstract class EstateEloquentRepository implements AdminBaseRepository, BaseRepository
{
    protected string $modelNamespace;

    public function getAll(array $fields = []): Collection
    {
        $query = $this->modelNamespace::query();

        if ($fields) {
            $query->select($fields);
        }

        return $query->get();
    }

    public function getByPrimary(iterable $primaryKeys): Collection
    {
        return $this->modelNamespace::whereKey($primaryKeys)->get();
    }

    public function save(EstateModel $model): bool
    {
        return $model->save();
    }

    public function saveQuietly(EstateModel $model): bool
    {
        return $model->saveQuietly();
    }

    public function delete(EstateModel $model): bool
    {
        return $model->delete();
    }

    // Admin
    public function getAllForAdmin(): Collection
    {
        return $this->modelNamespace::adminList()->get();
    }

    public function getByPrimaryForAdmin(iterable $primaryKeys): Collection
    {
        return $this->modelNamespace::whereKey($primaryKeys)->adminList()->get();
    }
}
