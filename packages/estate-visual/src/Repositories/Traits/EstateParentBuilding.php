<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kelnik\Estate\Models\Building;
use Kelnik\EstateVisual\Models\Casts\ExplodeInt;

trait EstateParentBuilding
{
    public function getParent(iterable $primaryKeys, ?string $prevStepName = null): Collection
    {
        $model = new Building();
        $relation = $model->{$this->getParentRelationName()}();
        $related = $relation->getRelated();
        $relationTable = $related->getTable();
        $tableName = $model->getTable();

        return Building::query()
            ->select([
                $tableName . '.id',
                $tableName . '.title',
                DB::raw(
                    'GROUP_CONCAT(' . $relation->getGrammar()->wrapTable($relationTable) . '.`id` ) as elementIds'
                )
            ])
            ->join(
                $relationTable,
                $relation->getQualifiedForeignKeyName(),
                '=',
                $relation->getQualifiedParentKeyName()
            )
            ->whereIn($relationTable . '.' . $relation->getModel()->getKeyName(), $primaryKeys)
            ->groupBy($tableName . '.' . $model->getKeyName())
            ->withCasts(['elementIds' => ExplodeInt::class])
            ->get();
    }
}
