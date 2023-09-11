<?php

declare(strict_types=1);

namespace Kelnik\Document\Repositories;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Document\Models\Element;
use Kelnik\Document\Repositories\Contracts\ElementRepository;

final class ElementEloquentRepository implements ElementRepository
{
    /** @var class-string<Element> $model */
    private $model = Element::class;

    public function isUnique(Model $model): bool
    {
        $query = $this->model::query()
                    ->where('category_id', '=', (int)$model->category_id)
                    ->limit(1);

        if ($model->exists) {
            $query->whereKeyNot($model->id);
        }

        return $query->get('id')->count() === 0;
    }

    public function findByPrimary(int|string $primary): Element
    {
        return $this->model::findOrNew($primary);
    }

    /**
     * @param Element $model
     *
     * @return bool
     */
    public function save(Model $model): bool
    {
        return $model->save();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
