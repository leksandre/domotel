<?php

declare(strict_types=1);

namespace Kelnik\News\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Repositories\Contracts\ElementRepository;

final class ElementEloquentRepository implements ElementRepository
{
    /** @var class-string $model */
    private $model = Element::class;

    /**
     * @param Element $model
     * @return bool
     */
    public function isUnique(Model $model): bool
    {
        $query = $this->model::query()
                    ->where('category_id', '=', (int)$model->category_id)
                    ->where('slug', '=', $model->slug)
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

    public function findActiveByPrimary(int|string $primary, ?array $fields = null): Element
    {
        $res = $this->model::query()
            ->whereKey($primary)
            ->whereHas('category', static fn($query) => $query->where('active', true)->limit(1))
            ->with([
                'category' => static fn($query) => $query->select('id', 'slug', 'title'),
                'images'
            ]);

        $this->addActiveFilter($res);

        if ($fields) {
            if (!in_array('category_id', $fields)) {
                $fields[] = 'category_id';
            }
            $res->select($fields);
        }

        return $res->firstOrNew();
    }

    public function findActiveBySlug(string $slug): Element
    {
        $res = $this->model::query()
                ->where('slug', $slug)
                ->whereHas('category', static fn($query) => $query->where('active', true)->limit(1))
                ->with([
                    'category' => static fn($query) => $query->select('id', 'slug', 'title'),
                    'bodyImage',
                    'images'
                ]);

        $this->addActiveFilter($res);

        return $res->firstOrNew();
    }

    public function getList(array $categories, int $limit = 0, int $offset = 0): Collection
    {
        $query = $this->getListQuery($categories)->orderByDesc('publish_date')->orderByDesc('id');

        if ($offset) {
            $query->offset($offset);
        }

        return $limit
            ? $query->limit($limit)->get()
            : $query->get();
    }

    public function getListWithExcludedRow(array $categories, int $excludeId, int $limit): Collection
    {
        return $this->getListQuery($categories)
                ->where('id', '!=', $excludeId)
                ->orderByDesc('publish_date')->orderByDesc('id')
                ->limit($limit)
                ->get();
    }

    private function getListQuery(array $categories): Builder
    {
        $query = $this->model::query()
            ->select([
                 'id', 'category_id', 'preview_image', 'body_image',
                 'active_date_start',
                 'active_date_finish',
                 'publish_date',
                 'publish_date_start',
                 'publish_date_finish',
                 'button',
                 'slug', 'title', 'preview', 'body'
             ])
            ->with([
               'category' => static fn($categoryQuery) => $categoryQuery->select(['id', 'slug', 'title']),
               'previewImage'
           ]);

        $this->addActiveFilter($query);

        $query->whereHas('category', static function ($categoryQuery) use ($categories) {
            $categoryQuery->select('id')->active()->limit(1);

            if ($categories) {
                $categoryQuery->whereIn('id', $categories);
            }
        });

        return $query;
    }

    public function getAdminListPaginatedBySelection(string $selectionClassName): LengthAwarePaginator
    {
        return $this->model::filters($selectionClassName)->defaultSort('id', 'desc')->with('category')->paginate();
    }

    public function getAdminListPaginatedBySelectionAndCategory(
        Category $category,
        string $selectionClassName
    ): LengthAwarePaginator {
        return $this->model::filters($selectionClassName)
            ->defaultSort('id', 'desc')
            ->with('category')
            ->where('category_id', $category->getKey())
            ->paginate();
    }

    private function addActiveFilter(&$query): void
    {
        $query->where('active', true)
            ->where(function (Builder $query) {
                $query->whereNull('active_date_start')->orWhere('active_date_start', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('active_date_finish')->orWhere('active_date_finish', '>=', now());
            });
    }

    /**
     * @param Element $model
     * @param array $imageIds
     *
     * @return bool
     */
    public function save(Model $model, array $imageIds = []): bool
    {
        $res = $model->save();

        if ($res) {
            $model->images()->syncWithoutDetaching($imageIds);
        }

        return $res;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
