<?php

declare(strict_types=1);

namespace Kelnik\Document\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Document\Models\Category;

interface CategoryRepository extends BaseRepository
{
    public function isUnique(Category $model): bool;

    public function findByPrimary(int|string $primary): Category;

    public function getAll(): Collection;

    public function getAdminList(): LengthAwarePaginator;

    public function getActiveWithElements(?int $group = null): Collection;
}
