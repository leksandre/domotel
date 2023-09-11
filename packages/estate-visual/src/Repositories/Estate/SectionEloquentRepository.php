<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Estate;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kelnik\Estate\Models\Section;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\SectionRepository;
use Kelnik\EstateVisual\Repositories\Traits\EstateParentBuilding;

final class SectionEloquentRepository implements SectionRepository
{
    use EstateParentBuilding;

    protected string $modelNamespace = Section::class;

    public function findByPrimary(int|string $primary): Section
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getParentRelationName(): string
    {
        return 'sections';
    }

    public function getForAdminByComplexPrimary(int|string $complexPrimary): Collection
    {
        return $complexPrimary
            ? $this->modelNamespace::whereHas(
                'building',
                function (Builder $query) use ($complexPrimary) {
                    if ($complexPrimary) {
                        $query->where('complex_id', $complexPrimary);
                    }

                    $query->select(['id'])->limit(1);
                }
            )
                ->orderBy('priority')
                ->orderBy('title')
                ->get(['id', 'building_id', 'title', DB::raw($complexPrimary . ' as complex_id')])
            : new Collection();
    }
}
