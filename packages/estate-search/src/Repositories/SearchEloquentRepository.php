<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Repositories\Traits\LoadAttachments;
use Kelnik\EstateSearch\Repositories\Contracts\SearchRepository;

final class SearchEloquentRepository implements SearchRepository
{
    use LoadAttachments;

    public function getPriceValues(Collection $filters): Premises
    {
        $query = Premises::query()->selectRaw('MAX(price_total) as max_value, MIN(price_total) as min_value');
        $query = $this->applyFilter($query, $filters);

        return $query->get()->first();
    }

    public function getAreaValues(Collection $filters): Premises
    {
        $query = Premises::query()->selectRaw('MAX(area_total) as max_value, MIN(area_total) as min_value');
        $query = $this->applyFilter($query, $filters);

        return $query->get()->first();
    }

    public function getTypeValues(Collection $filters): Collection
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

    public function getTypesByGroup(array $typeGroupIds): Collection
    {
        return $typeGroupIds
            ? PremisesType::query()
                ->select('id', 'rooms', 'title', 'short_title', 'slug')
                ->whereIn('group_id', $typeGroupIds)
                ->orderBy('priority')
                ->orderBy('title')
                ->get()
            : new Collection();
    }

    public function getStatusValues(Collection $filters, array $allowStatuses): Collection
    {
        $subQuery = Premises::query()->select('status_id')->groupBy('status_id');
        $subQuery = $this->applyFilter($subQuery, $filters);

        $query = PremisesStatus::query()
            ->select('id', 'title')
            ->whereIn('id', $subQuery);

        if ($allowStatuses) {
            $query->whereIn('id', $allowStatuses);
        }

        return $query->orderBy('priority')
            ->orderBy('title')
            ->get();
    }

    public function getFloorValues(Collection $filters): Floor
    {
        $subQuery = Premises::query()->select('floor_id')->groupBy('floor_id');
        $subQuery = $this->applyFilter($subQuery, $filters);

        return Floor::query()
            ->selectRaw('MAX(number) as max_value, MIN(number) as min_value')
            ->whereIn('id', $subQuery)
            ->orderBy('priority')
            ->orderBy('number')
            ->get()
            ->first();
    }

    public function getBuildingValues(Collection $filters): Collection
    {
        $building = new Building();
        $buildingTable = $building->getTable();
        $floorRelation = $building->floors();
        $floorTable = $floorRelation->getRelated()->getTable();

        $subQuery = Premises::query()->select('floor_id')->groupBy('floor_id');
        $subQuery = $this->applyFilter($subQuery, $filters);

        return Building::query()
            ->select([
                $buildingTable . '.id',
                $buildingTable . '.title',
                $buildingTable . '.slug'
            ])
            ->join(
                $floorTable,
                $floorRelation->getQualifiedParentKeyName(),
                '=',
                $floorRelation->getQualifiedForeignKeyName()
            )
            ->whereIn($floorTable . '.id', $subQuery)
            ->groupBy($buildingTable . '.id')
            ->orderBy($buildingTable . '.priority')
            ->orderBy($buildingTable . '.title')
            ->get();
    }

    public function getSectionValues(Collection $filters): Collection
    {
        $subQuery = Premises::query()->select('section_id')->groupBy('section_id');
        $subQuery = $this->applyFilter($subQuery, $filters);

        return Section::query()
            ->select(['id', 'title', 'slug'])
            ->whereIn('id', $subQuery)
            ->groupBy('id')
            ->orderBy('priority')
            ->orderBy('title')
            ->get();
    }

    public function getCompletionValues(Collection $filters): Collection
    {
        $completion = new Completion();
        $completionTable = $completion->getTable();

        $buildingRelation = $completion->buildings();
        $building = $buildingRelation->getRelated();
        $buildingTable = $building->getTable();
        $floorRelation = $building->floors();
        $floorTable = $floorRelation->getRelated()->getTable();

        $subQuery = Premises::query()->select('floor_id')->groupBy('floor_id');
        $subQuery = $this->applyFilter($subQuery, $filters);

        return Completion::query()
            ->select([
                $completionTable . '.id',
                $completionTable . '.event_date',
                $completionTable . '.title',
            ])
            ->join(
                $buildingTable,
                $buildingRelation->getQualifiedParentKeyName(),
                '=',
                $buildingRelation->getQualifiedForeignKeyName()
            )
            ->join(
                $floorTable,
                $floorRelation->getQualifiedParentKeyName(),
                '=',
                $floorRelation->getQualifiedForeignKeyName()
            )
            ->whereIn($floorTable . '.id', $subQuery)
            ->orderBy($completionTable . '.priority')
            ->orderBy($completionTable . '.event_date')
            ->orderBy($completionTable . '.title')
            ->groupBy($completionTable . '.id')
            ->get();
    }

    public function getFeatureValues(Collection $filters): Collection
    {
        $feature = new PremisesFeature();
        $featureTable = $feature->getTable();
        $premisesRelation = $feature->premises();
        $pivot = $premisesRelation->getPivotClass();
        $throughTable = (new $pivot())->getTable();

        $subQuery = Premises::query()->select('id')->groupBy('id');
        $subQuery = $this->applyFilter($subQuery, $filters);

        return PremisesFeature::query()
            ->select([
                $featureTable . '.id',
                $featureTable . '.group_id',
                $featureTable . '.icon_id',
                $featureTable . '.title',
            ])
            ->join(
                $throughTable,
                $premisesRelation->getQualifiedParentKeyName(),
                '=',
                $premisesRelation->getQualifiedForeignPivotKeyName()
            )
            ->with([
                'icon',
                'featureGroup' => fn(BelongsTo $belongsTo) => $belongsTo->select(['id', 'priority', 'general', 'title'])
            ])
            ->active()
            ->whereIn($premisesRelation->getQualifiedRelatedPivotKeyName(), $subQuery)
            ->whereHas(
                'featureGroup',
                fn(Builder $builder) => $builder->select('id')->active()->limit(1)
            )
            ->orderBy($featureTable . '.priority')
            ->orderBy($featureTable . '.title')
            ->groupBy($featureTable . '.id')
            ->get()
            ->append('full_title');
    }

    public function hasPromotionalPremises(Collection $filters): bool
    {
        return $this->applyFilter(
            Premises::query()->select('id')->where('action', true)->limit(1),
            $filters
        )->get()->isNotEmpty();
    }

    public function hasPremisesByFilter(Collection $filters): bool
    {
        return $this->applyFilter(
            Premises::query()->select('id')->limit(1),
            $filters
        )->get()->isNotEmpty();
    }

    public function getCount(Collection $filters): int
    {
        return (int)($this->applyFilter(
            Premises::query()->selectRaw('COUNT(id) as cnt'),
            $filters
        )->get()?->first()?->cnt ?? 0);
    }

    public function getResults(
        Collection $filters,
        Collection $orders,
        int $limit = 0,
        int $offset = 0
    ): array|Collection|LazyCollection {
        $query = Premises::query()
            ->select([
                'id',
                'type_id',
                'status_id',
                'floor_id',
                'section_id',
                'plan_type_id',
                'active',
                'rooms',
                'number',
                'title',
                'price',
                'price_total',
                'price_sale',
                'price_meter',
                'price_rent',
                'area_total',
                'area_living',
                'area_kitchen',
                'image_list_id',
                'image_plan_id',
                'image_plan_furniture_id',
                'image_3d_id',
                'image_on_floor_id',
                'external_id',
                'planoplan_code'
            ])
            ->with([
                'status' => fn(BelongsTo $builder) => $builder->premisesCard(),
                'section' => fn(BelongsTo $builder) => $builder->premisesCard(),
                'floor' => fn(BelongsTo $builder) => $builder->premisesCard(),
                'type' => fn(BelongsTo $builder) => $builder->premisesCard(),
                'features' => fn(BelongsToMany $builder) => $builder->premisesCard(),
                'planoplan' => fn(HasOne $builder) => $builder->active()
            ]);

        foreach (['limit', 'offset'] as $param) {
            if (${$param}) {
                $query->{$param}(${$param});
            }
        }

        $query = $this->applyFilter($query, $filters);
        $query = $this->applySortOrder($query, $orders);

        return $this->loadAttachments(
            $query->get(),
            [
                'image_list_id' => 'imageList',
                'image_plan_id' => 'imagePlan',
                'image_plan_furniture_id' => 'imagePlanFurniture',
                'image_3d_id' => 'image3D',
                'image_on_floor_id' => 'imageOnFloor'
            ]
        );
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

    private function applySortOrder(Builder $builder, Collection $orders): Builder
    {
        if ($orders->isEmpty()) {
            return $builder;
        }

        $premisesModel = new Premises();

        foreach ($orders as $fieldName => $direction) {
            if (!str_contains($fieldName, '.')) {
                $builder->orderBy($fieldName, $direction);
                continue;
            }

            $fields = explode('.', $fieldName);
            $relationName = array_shift($fields);
            $relation = call_user_func([$premisesModel, $relationName]);

            if (!$relation instanceof BelongsTo) {
                continue;
            }

            $relatedModel = $relation->getRelated();

            $subQuery = $relatedModel::query()
                ->select([
                    $relatedModel->getTable() . '.' . array_shift($fields)
                ])
                ->whereColumn(
                    $relation->getQualifiedForeignKeyName(),
                    '=',
                    $relation->getQualifiedOwnerKeyName()
                );

            $builder->orderBy($subQuery, $direction);
        }

        return $builder;
    }
}
