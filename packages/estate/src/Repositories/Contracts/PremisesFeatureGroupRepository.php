<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\PremisesFeatureGroup;

interface PremisesFeatureGroupRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): PremisesFeatureGroup;

    public function getAllWithFeatures(): Collection;

    public function save(EstateModel $model, ?array $features = null): bool;

    public function getGeneral(): PremisesFeatureGroup;

    public function getGeneralKey(): int;
}
