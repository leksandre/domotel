<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeRepository;

final class PremisesTypeEloquentRepository extends EstateEloquentRepository implements PremisesTypeRepository
{
    protected string $modelNamespace = PremisesType::class;

    public function findByPrimary(int|string $primary): PremisesType
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function isUnique(PremisesType $premisesType): bool
    {
        $query = $this->modelNamespace::query()->where('slug', '=', $premisesType->slug)->limit(1);

        if ($premisesType->exists) {
            $query->whereKeyNot($premisesType->id);
        }

        return $query->get('id')->count() === 0;
    }

    public function getByGroupPrimary(int|string $primary): Collection
    {
        return $this->modelNamespace::where('group_id', $primary)->adminList()->get();
    }
}
