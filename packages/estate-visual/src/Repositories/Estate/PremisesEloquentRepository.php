<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Estate;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Premises;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesRepository;
use Kelnik\EstateVisual\Repositories\Traits\EstateParentBuilding;

final class PremisesEloquentRepository implements PremisesRepository
{
    use EstateParentBuilding;

    protected string $modelNamespace = Premises::class;

    public function getForAdminByComplexPrimary(int|string $complexPrimary): Collection
    {
        return new Collection();
    }

    public function getByFloorPrimary(iterable $primaryKeys): Collection
    {
        return $this->modelNamespace::query()
            ->select(['id', 'type_id', 'rooms', 'number', 'area_total', 'title', 'external_id'])
            ->whereIn('floor_id', $primaryKeys)
            ->with(['type', 'type.typeGroup'])
            ->get();
    }
}
