<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Estate;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Building;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\BuildingRepository;

final class BuildingEloquentRepository implements BuildingRepository
{
    protected string $modelNamespace = Building::class;

    public function findByPrimary(int|string $primary): Building
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getForAdminByComplexPrimary(int|string $complexPrimary): Collection
    {
        return $complexPrimary
            ? $this->modelNamespace::where('complex_id', $complexPrimary)
                ->orderBy('priority')
                ->orderBy('title')
                ->get(['id', 'complex_id', 'title'])
            : new Collection();
    }
}
