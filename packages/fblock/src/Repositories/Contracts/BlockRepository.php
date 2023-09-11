<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\FBlock\Models\FlatBlock;

interface BlockRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): FlatBlock;

    public function findActiveByPrimary(int|string $primary, ?array $fields = null): FlatBlock;

    public function getActiveList(int $limit, int $offset): Collection;

    public function getAll(): Collection;

    public function save(Model $model, array $imageIds = []): bool;
}
