<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Estate;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\Section;
use Kelnik\EstateVisual\Models\Casts\ExplodeInt;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\FloorRepository;
use Kelnik\EstateVisual\Repositories\Traits\EstateParentBuilding;

final class FloorEloquentRepository implements FloorRepository
{
    use EstateParentBuilding {
        EstateParentBuilding::getParent as getTraitEstateParent;
    }

    protected string $modelNamespace = Floor::class;

    public function findByPrimary(int|string $primary): Floor
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getParentRelationName(): string
    {
        return 'floors';
    }

    public function getForAdminByComplexPrimary(int|string $complexPrimary): Collection
    {
        if (!$complexPrimary) {
            return new Collection();
        }

        /** @var Collection $res */
        $res = $this->modelNamespace::whereHas(
            'building',
            static fn(Builder $query) => $query->where('complex_id', $complexPrimary)->select('id')->limit(1)
        )
            ->orderBy('priority')
            ->orderBy('number')
            ->orderBy('title')
            ->get(['id', 'building_id', 'title', DB::raw($complexPrimary . ' as complex_id')]);

        if ($res->isEmpty()) {
            return $res;
        }

        $sectionFloors = Premises::query()->select([
                'floor_id',
                DB::raw('GROUP_CONCAT(DISTINCT `section_id`) as sectionIds')
            ])
            ->whereIn('floor_id', $res->pluck('id')->toArray())
            ->groupBy('floor_id')
            ->withCasts(['sectionIds' => ExplodeInt::class])
            ->get()
            ->pluck('sectionIds', 'floor_id');

        /** @var Floor $el */
        foreach ($res as &$el) {
            $el->setAttribute('section_id', $sectionFloors->get($el->getKey(), []));
        }
        unset($sectionFloors);

        return $res;
    }

    public function getParent(iterable $primaryKeys, ?string $prevStepName = null): Collection
    {
        if ($prevStepName === 'complex') {
            return $this->getParentComplex($primaryKeys);
        } elseif ($prevStepName === 'building') {
            return $this->getTraitEstateParent($primaryKeys, $prevStepName);
        }

        $model = new Section();
        $tableName = $model->getTable();

        /** @var Premises $premisesRelated */
        $premisesRelation = $model->premises();
        $premisesRelated = $premisesRelation->getRelated();
        $premisesTable = $premisesRelated->getTable();

        /** @var Floor $floorsRelated */
        $floorsRelation = $premisesRelated->floor();
        $floorsRelated = $floorsRelation->getRelated();
        $floorsTable = $floorsRelated->getTable();

        return Section::query()
            ->select([
                $tableName . '.id',
                $tableName . '.building_id',
                $tableName . '.title',
                DB::raw(
                    'GROUP_CONCAT(DISTINCT ' .
                    $floorsRelation->getGrammar()->wrapTable($floorsTable) .
                    '.`id` ) as elementIds'
                )
            ])
            ->with([
                'building' => fn($query) => $query->select(['id', 'title'])
            ])
            ->join(
                $premisesTable,
                $premisesRelation->getQualifiedForeignKeyName(),
                '=',
                $premisesRelation->getQualifiedParentKeyName()
            )
            ->join(
                $floorsTable,
                $floorsRelation->getQualifiedForeignKeyName(),
                '=',
                $floorsRelation->getQualifiedOwnerKeyName()
            )
            ->whereIn($floorsTable . '.' . $floorsRelation->getModel()->getKeyName(), $primaryKeys)
            ->groupBy($tableName . '.' . $model->getKeyName())
            ->orderBy($tableName . '.priority')
            ->withCasts(['elementIds' => ExplodeInt::class])
            ->get()
            ->each(static function (Section $el) {
                $el->groupTitle = $el->building->title;
                $el->groupName = 'building';
                $el->unsetRelation('building');
            });
    }

    private function getParentComplex(iterable $primaryKeys): Collection
    {
        return $this->getTraitEstateParent($primaryKeys, 'complex');
    }
}
