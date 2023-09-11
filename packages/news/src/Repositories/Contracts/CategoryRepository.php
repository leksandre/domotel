<?php

declare(strict_types=1);

namespace Kelnik\News\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\News\Models\Category;

interface CategoryRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Category;

    public function getAll(): Collection;
}
