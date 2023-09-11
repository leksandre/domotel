<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesFeature;

interface PremisesFeatureRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): PremisesFeature;

    public function getByGroupPrimary(int|string $primary): Collection;
}
