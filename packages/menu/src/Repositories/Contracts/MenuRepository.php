<?php

declare(strict_types=1);

namespace Kelnik\Menu\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Menu\Models\Menu;

interface MenuRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Menu;

    public function findActiveByPrimary(int|string $primary): Menu;

    public function getAll(): Collection;

    public function getAdminList(): LengthAwarePaginator;

    public function findByPageOrPageComponent(int|string $pageKey, int|string $pageComponentKey): Collection;

    public function save(Model $model, ?array $items = null): bool;
}
