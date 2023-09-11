<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureRepository;

final class PremisesFeatureEloquentRepository extends EstateEloquentRepository implements PremisesFeatureRepository
{
    protected string $modelNamespace = PremisesFeature::class;

    public function findByPrimary(int|string $primary): PremisesFeature
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getByGroupPrimary(int|string $primary): Collection
    {
        return $this->modelNamespace::where('group_id', $primary)->adminList()->get();
    }
}
