<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Estate;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kelnik\Estate\Models\Completion;
use Kelnik\EstateVisual\Models\Casts\ExplodeInt;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\CompletionRepository;

final class CompletionEloquentRepository implements CompletionRepository
{
    protected string $modelNamespace = Completion::class;

    public function getByBuildingPrimaryKeys(array $buildingKeys): Collection
    {
        if (!$buildingKeys) {
            return new Collection();
        }

        $model = new Completion();
        $relation = $model->buildings();
        $related = $relation->getRelated();
        $relationTable = $related->getTable();
        $tableName = $model->getTable();

        return Completion::query()
            ->select([
                $tableName . '.id',
                $tableName . '.title',
                $tableName . '.event_date',
                DB::raw(
                    'GROUP_CONCAT(' . $relation->getGrammar()->wrapTable($relationTable) . '.`id` ) as buildingIds'
                )
            ])
            ->join(
                $relationTable,
                $relation->getQualifiedForeignKeyName(),
                '=',
                $relation->getQualifiedParentKeyName()
            )
            ->whereIn($relationTable . '.' . $relation->getModel()->getKeyName(), $buildingKeys)
            ->groupBy($tableName . '.' . $model->getKeyName())
            ->withCasts([
                'id' => 'integer',
                'event_date' => 'date',
                'buildingIds' => ExplodeInt::class
            ])
            ->get();
    }
}
