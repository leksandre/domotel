<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Kelnik\Estate\Models\PremisesPlanType;
use Kelnik\Estate\Repositories\Contracts\PremisesPlanTypeRepository;

final class PremisesPlanTypeEloquentRepository extends EstateEloquentRepository implements PremisesPlanTypeRepository
{
    protected string $modelNamespace = PremisesPlanType::class;

    public function findByPrimary(int|string $primary): PremisesPlanType
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function isUnique(PremisesPlanType $premisesPlanType): bool
    {
        $query = $this->modelNamespace::query()->where('slug', '=', $premisesPlanType->slug)->limit(1);

        if ($premisesPlanType->exists) {
            $query->whereKeyNot($premisesPlanType->id);
        }

        return $query->get('id')->count() === 0;
    }
}
