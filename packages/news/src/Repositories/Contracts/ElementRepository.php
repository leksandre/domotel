<?php

declare(strict_types=1);

namespace Kelnik\News\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;

interface ElementRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Element;

    public function findActiveByPrimary(int|string $primary, ?array $fields = null): Element;

    public function findActiveBySlug(string $slug): Element;

    public function getList(array $categories, int $limit, int $offset): Collection;

    public function getListWithExcludedRow(array $categories, int $excludeId, int $limit): Collection;

    public function getAdminListPaginatedBySelection(string $selectionClassName): LengthAwarePaginator;

    public function getAdminListPaginatedBySelectionAndCategory(
        Category $category,
        string $selectionClassName
    ): LengthAwarePaginator;

    public function save(Model $model, array $imageIds = []): bool;
}
