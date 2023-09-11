<?php

declare(strict_types=1);

namespace Kelnik\Core\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Site;

interface SiteRepository
{
    public function findByPrimaryKey(int|string $primary): Site;

    public function findPrimary(): Site;

    public function getAdminList(): LengthAwarePaginator;

    public function getList(): LengthAwarePaginator;

    public function getAll(): Collection;

    public function save(Site $model, array $hosts = []): bool;

    public function delete(Site $model): bool;

    public function getActive(): Collection;

    public function hasSite(): bool;
}
