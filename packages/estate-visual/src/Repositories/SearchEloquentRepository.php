<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Repositories\Traits\LoadAttachments;
use Kelnik\EstateVisual\Repositories\Contracts\SearchRepository;

final class SearchEloquentRepository implements SearchRepository
{
    use LoadAttachments;

    public function getPriceValues(Collection $filters): Premises
    {
        $query = Premises::query()->selectRaw('MAX(price_total) as max_value, MIN(price_total) as min_value');
        $query = $this->applyFilter($query, $filters);

        return $query->get()->first();
    }

    public function getRoomValues(Collection $filters): Collection
    {
        $subQuery = Premises::query()->select('type_id')->groupBy('type_id');
        $subQuery = $this->applyFilter($subQuery, $filters);

        return PremisesType::query()
            ->select('id', 'rooms', 'title', 'short_title', 'slug')
            ->whereIn('id', $subQuery)
            ->orderBy('priority')
            ->orderBy('title')
            ->get();
    }

    public function getPremises(Collection $filters, array $primaryKeys): Collection|LazyCollection
    {
        $model = new Premises();
        $tableName = $model->getTable();
        $status = $model->status();
        $statusTable = $status->getRelated()->getTable();

        $query = $model::query()
            ->select([
                $tableName . '.id',
                $tableName . '.type_id',
                $tableName . '.status_id',
                $tableName . '.floor_id',
                $tableName . '.section_id',
                $tableName . '.plan_type_id',
                $tableName . '.active',
                $tableName . '.rooms',
                $tableName . '.number',
                $tableName . '.title',
                $tableName . '.price',
                $tableName . '.price_total',
                $tableName . '.price_sale',
                $tableName . '.price_meter',
                $tableName . '.price_rent',
                $tableName . '.area_total',
                $tableName . '.area_living',
                $tableName . '.area_kitchen',
                $tableName . '.image_list_id',
                $tableName . '.image_plan_id',
                $tableName . '.external_id',
                $tableName . '.planoplan_code'
            ])
            ->join($statusTable, $status->getQualifiedOwnerKeyName(), '=', $status->getQualifiedForeignKeyName())
            ->with([
                'status' => fn(BelongsTo $builder) => $builder->premisesCard(),
                'section' => fn(BelongsTo $builder) => $builder->premisesCard(),
                'floor' => fn(BelongsTo $builder) => $builder->premisesCard(),
                'type' => fn(BelongsTo $builder) => $builder->premisesCard(),
                'features' => fn(BelongsToMany $builder) => $builder->premisesCard(),
                'planoplan' => fn(HasOne $builder) => $builder->active()
            ])
            ->whereKey($primaryKeys);

        $query = $this->applyFilter($query, $filters);

        return $this->loadAttachments(
            $query->get(),
            [
                'image_list_id' => 'imageList',
                'image_plan_id' => 'imagePlan'
            ]
        );
    }

    public function getFloorsWithPremisesCount(Collection $filters): Collection
    {
        $model = new Premises();
        $tableName = $model->getTable();

        $query = $model::query()
            ->select([$tableName . '.floor_id', DB::raw('COUNT(*) as cnt')])
            ->groupBy([$tableName . '.floor_id']);

        return $this->applyFilter($query, $filters)->get()->pluck('cnt', 'floor_id');
    }

    public function getFloorIdsWithPremisesStat(Collection $filters): Collection
    {
        $model = new Premises();
        $tableName = $model->getTable();
        $grammar = $model->getConnection()->getQueryGrammar();

        $query = $model::query()
            ->select([
                $tableName . '.type_id',
                $tableName . '.floor_id',
                $tableName . '.section_id',
                $this->getMinPriceExpression($grammar, $tableName),
                DB::raw('COUNT(*) as cnt')
            ])
            ->with([
                'type' => fn(BelongsTo $query) =>
                    $query->select(['id', 'priority', 'rooms', 'color', 'title', 'short_title']),
                'floor' => fn(BelongsTo $query) => $query->select(['id', 'building_id'])
            ])
            ->groupBy([$tableName . '.floor_id', $tableName . '.type_id']);

        return $this->applyFilter($query, $filters)
            ->get()
            ->sortBy(static fn(Premises $premises) => $premises->type->priority);
    }

    public function getSectionIdsWithPremisesStat(Collection $filters): Collection
    {
        $model = new Premises();
        $tableName = $model->getTable();
        $grammar = $model->getConnection()->getQueryGrammar();

        $query = $model::query()
            ->select([
                $tableName . '.type_id',
                $tableName . '.section_id',
                $this->getMinPriceExpression($grammar, $tableName),
                DB::raw('COUNT(*) as cnt')
            ])
            ->with([
                'type' => fn(BelongsTo $query) =>
                    $query->select(['id', 'priority', 'rooms', 'color', 'title', 'short_title']),
                'section' => fn(BelongsTo $query) => $query->select(['id', 'building_id'])
            ])
            ->groupBy([$tableName . '.section_id', $tableName . '.type_id']);

        return $this->applyFilter($query, $filters)
            ->get()
            ->sortBy(static fn(Premises $premises) => $premises->type->priority);
    }

    public function getBuildingIdsWithPremisesStat(Collection $filters): Collection
    {
        $model = new Premises();
        $tableName = $model->getTable();
        $grammar = $model->getConnection()->getQueryGrammar();

        /** @var Floor $floorRelated */
        $floorRelation = $model->floor();
        $floorRelated = $floorRelation->getRelated();
        $floorTable = $floorRelated->getTable();

        $buildingRelation = $floorRelated->building();
        $buildingRelated = $buildingRelation->getRelated();
        $buildingTable = $buildingRelated->getTable();

        $query = $model::query()
            ->select([
                $tableName . '.type_id',
                $buildingTable . '.id as buildingId',
                $this->getMinPriceExpression($grammar, $tableName),
                DB::raw('COUNT(*) as cnt')
            ])
            ->join(
                $floorTable,
                $floorRelation->getQualifiedForeignKeyName(),
                '=',
                $floorRelation->getQualifiedOwnerKeyName()
            )
            ->join(
                $buildingTable,
                $buildingRelation->getQualifiedForeignKeyName(),
                '=',
                $buildingRelation->getQualifiedOwnerKeyName()
            )
            ->with([
                'type' => fn(BelongsTo $query) =>
                $query->select(['id', 'priority', 'rooms', 'color', 'title', 'short_title'])
            ])
            ->groupBy([$buildingTable . '.id', $tableName . '.type_id']);

        return $this->applyFilter($query, $filters)
            ->get()
            ->sortBy(static fn(Premises $premises) => $premises->type->priority);
    }

    private function applyFilter(Builder $builder, Collection $filters): Builder
    {
        if ($filters->isEmpty()) {
            return $builder;
        }

        $newFilters = [];

        $filters->each(function (array $param) use (&$newFilters) {
            $newFilters = array_merge_recursive($newFilters, $this->parseFilter($param));
        });

        if ($newFilters) {
            if (!empty($newFilters['fields'])) {
                $tableName = $builder->getModel()->getTable();
                foreach ($newFilters['fields'] as &$fieldFilter) {
                    $fieldFilter[0] = $tableName . '.' . $fieldFilter[0];
                }
                unset($fieldFilter);
            }
            $this->applyFilterNested($builder, $newFilters);
        }

        return $builder;
    }

    private function parseFilter(array $param): array
    {
        $names = explode('.', $param[0]);
        $res = [];

        if (count($names) < 2) {
            $res['fields'][] = $param;

            return $res;
        }

        $relationName = array_shift($names);
        $res['relations'][$relationName] = $this->parseFilter([
            implode('.', $names),
            $param[1],
            $param[2] ?? null
        ]);

        return $res;
    }

    private function applyFilterNested(Builder $builder, array $filter): void
    {
        if (!empty($filter['fields'])) {
            foreach ($filter['fields'] as $param) {
                $this->applyWhere($builder, ...$param);
            }
        }

        if (empty($filter['relations'])) {
            return;
        }

        foreach ($filter['relations'] as $relation => $relFilter) {
            $builder->whereHas($relation, function (Builder $b) use ($relFilter, $relation) {
                $b->select($b->getModel()->getQualifiedKeyName())->limit(1);
                $this->applyFilterNested($b, $relFilter);
            });
        }
    }

    private function applyWhere(Builder $builder, string $fieldName, string|Closure $operator, $value = null): void
    {
        if ($operator instanceof Closure) {
            call_user_func($operator, $builder);

            return;
        }

        if ($operator === 'in') {
            $builder->whereIn($fieldName, $value);

            return;
        }

        $builder->where($fieldName, $operator, $value);
    }

    private function getMinPriceExpression($grammar, string $tableName): Expression
    {
        return DB::raw(
            'MIN(' .
                'CASE ' .
                    'WHEN (' . $grammar->wrap($tableName . '.price_total') . ' > 0) ' .
                        'THEN ' . $grammar->wrap($tableName . '.price_total') . ' ' .
                    'ELSE ' . $grammar->wrap($tableName . '.price') . ' ' .
                'END' .
            ') as price_min'
        );
    }
}
