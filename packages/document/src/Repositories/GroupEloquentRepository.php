<?php

declare(strict_types=1);

namespace Kelnik\Document\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Document\Models\Group;
use Kelnik\Document\Repositories\Contracts\GroupRepository;

final class GroupEloquentRepository implements GroupRepository
{
    /** @var class-string<Group>  */
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
