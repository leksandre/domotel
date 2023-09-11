<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\PremisesFeatureReference;

interface PremisesFeatureReferenceRepository
{
    public function findByPrimary(int|string $primary): PremisesFeatureReference;

    public function getAll(array $fields = []): Collection;

    public function getAllLazy(): LazyCollection;

    public function deleteManyByPrimary(iterable $primaryKeys): int;

    public function addMany(iterable $entries): int;
}
