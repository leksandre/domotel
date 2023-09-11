<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\PremisesTypeGroup;

interface PremisesTypeGroupRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): PremisesTypeGroup;

    public function isUnique(PremisesTypeGroup $premisesTypeGroup): bool;

    public function getAllWithTypes(): Collection;

    public function save(EstateModel $model, ?array $features = null): bool;
}
