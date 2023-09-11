<?php

declare(strict_types=1);

namespace Kelnik\News\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\News\Models\Category;
use Kelnik\News\Repositories\Contracts\CategoryRepository;

final class CategoryEloquentRepository implements CategoryRepository
{
    /** @var class-string $model */
    private $model = Category::class;

    public function isUnique(Model $model): bool
    {
        $query = $this->model::query()->where('slug', '=', $model->slug)->limit(1);

        if ($model->exists) {
            $query->whereKeyNot($model->id);
        }

        return $query->get('id')->count() === 0;
    }

    public function findByPrimary(int|string $primary): Category
    {
        return $this->model::findOrNew($primary);
    }

    public function getAll(): Collection
    {
        return $this->model::query()->orderBy('priority')->orderBy('title')->get();
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
