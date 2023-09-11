<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Kelnik\Estate\Repositories\Traits\LoadAttachments;
use Kelnik\Estate\View\Components\RecommendList\Contracts\Filter;

final class PremisesEloquentRepository extends EstateEloquentRepository implements PremisesRepository
{
    use LoadAttachments;

    protected string $modelNamespace = Premises::class;

    public function findByPrimary(int|string $primary): Premises
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getAdminList(): LengthAwarePaginator
    {
        return $this->modelNamespace::filters()
            ->defaultSort('id', 'desc')
            ->with([
                'floor' => fn(BelongsTo $builder) => $builder->adminList(),
                'section' => fn(BelongsTo $builder) => $builder->adminList(),
                'status' => fn(BelongsTo $builder) => $builder->select('id', 'title'),
                'features'
            ])
            ->paginate();
    }

    public function getAllBySelectionForAdmin(string $selectionClassName): Collection
    {
        return $this->getBySelection($selectionClassName)->get();
    }

    public function getAllBySelectionForAdminPaginated(string $selectionClassName): Paginator
    {
        return $this->getBySelection($selectionClassName)->paginate();
    }

    private function getBySelection(string $selectionClassName): Builder
    {
        return $this->modelNamespace::filters()
            ->filtersApplySelection($selectionClassName)
            ->with([
                'floor' => fn(BelongsTo $builder) => $builder->adminList(),
                'section' => fn(BelongsTo $builder) => $builder->adminList(),
                'status' => fn(BelongsTo $builder) => $builder->select('id', 'title'),
                'type' => fn(BelongsTo $builder) => $builder->adminList(),
                'features'
            ]);
    }

    public function getByFloorAndSection(array $floorPrimary, int|string $sectionPrimary): Collection
    {
        return Premises::query()
            ->select([
                'id', 'floor_id',
                'image_plan_id', 'image_list_id', 'image_on_floor_id',
                'number_on_floor',
                'number'
            ])
            ->whereIn('floor_id', $floorPrimary)
            ->when(
                $sectionPrimary,
                static fn(Builder $builder) => $builder->where('section_id', $sectionPrimary)
            )
            ->orderBy('floor_id')
            ->orderBy('number_on_floor')
            ->get();
    }

    public function save(EstateModel $model, ?array $features = null, ?array $gallery = null): bool
    {
        $res = $model->save();

        $model->gallery()->syncWithoutDetaching($gallery);

        if (!$features) {
            if (is_array($features)) {
                $model->features()->detach();
            }

            return $res;
        }

        $model->features()->sync($features);

        return $res;
    }

    /**
     * @param Premises $model
     * @param array|null $features
     * @param array|null $gallery
     * @return bool
     */
    public function saveQuietly(EstateModel $model, ?array $features = null, ?array $gallery = null): bool
    {
        $res = $model->save();

        $model->gallery()->syncWithoutDetaching($gallery);

        if (!$features) {
            if (is_array($features)) {
                $model->features()->detach();
            }

            return $res;
        }

        $model->features()->sync($features);

        return $res;
    }

    public function findByPrimaryForCard(int|string $primary): Premises
    {
        return $this->modelNamespace::with([
            'status' => fn(BelongsTo $builder) => $builder->premisesCard()
        ])->active()->findOrNew($primary);
    }

    public function loadFullCardData(Premises $premises): Premises
    {
        $premises->load([
            'section' => fn(BelongsTo $builder) => $builder->premisesCard(),
            'floor' => fn(BelongsTo $builder) => $builder->premisesCard(),
            'type' => fn(BelongsTo $builder) => $builder->premisesCard(),
            'features' => fn(BelongsToMany $builder) => $builder->premisesCard(),
            'planoplan' => fn(HasOne $builder) => $builder->active(),
            'gallery'
        ]);

        return $this->loadAttachments(new Collection([$premises]), $this->attachmentRelations())->first();
    }

    private function attachmentRelations(): array
    {
        return [
            'image_list_id' => 'imageList',
            'image_plan_id' => 'imagePlan',
            'image_plan_furniture_id' => 'imagePlanFurniture',
            'image_3d_id' => 'image3D',
            'image_on_floor_id' => 'imageOnFloor'
        ];
    }

    /**
     * 1) Учитываем комнатность
     * 2) Учитываем этаж и ищем как можно ближе к этому этажу
     * 3) Общая площадь в диапазоне +/-5 м2
     *
     * @param Filter $filter
     *
     * @return Collection
     * @throws Exception
     */
    public function getRecommends(Filter $filter): Collection
    {
        $model = new $this->modelNamespace();
        $tableName = $model->getTable();

        $floor = $model->floor();
        $floorTableName = $floor->getModel()->getTable();
        $queryGrammar = $model->getConnection()->getQueryGrammar();

        $query = Premises::query()
            ->distinct()
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
            ->selectRaw(
                'ABS(CAST(' . $queryGrammar->wrap($floorTableName . '.number') . ' AS SIGNED) - ?) ' .
                ' as `floor_diff`, ' .
                'CASE WHEN (' . $queryGrammar->wrap($tableName . '.type_id') . ' = ?) THEN 1 ELSE 0 END as `same_type`',
                [$filter->floorNum, $filter->typeKey]
            )
            ->join(
                $floor->getRelated()->getTable(),
                $floor->getQualifiedForeignKeyName(),
                '=',
                $floor->getQualifiedOwnerKeyName()
            )
            ->where($tableName . '.active', true)
            ->where($floorTableName . '.active', true)
            ->whereHas('floor', static function (Builder $builder) {
                return $builder
                    ->select(['id'])
                    ->where('active', true)
                    ->whereHas(
                        'building',
                        static fn (Builder $builder) => $builder
                            ->select('id')
                            ->where('active', true)
                            ->whereHas(
                                'complex',
                                static fn(Builder $builder) => $builder
                                    ->select('id')
                                    ->where('active', true)
                                    ->limit(1)
                            )
                            ->limit(1)
                    )
                    ->limit(1);
            })
            ->whereKeyNot($filter->excludeKey);

        if ($filter->typeGroupKey && $filter->typeKey) {
            $query->where(fn(Builder $builder) => $builder
                ->where($tableName . '.type_id', $filter->typeKey)
                ->orWhereHas(
                    'type',
                    static fn(Builder $subBuilder) => $subBuilder
                        ->select('id')
                        ->where('group_id', $filter->typeGroupKey)
                        ->limit(1)
                ));
        } elseif ($filter->typeKey) {
            $query->where($tableName . '.type_id', $filter->typeKey);
        }

        $query->whereHas(
            'status',
            static fn(Builder $builder) => $builder
                ->select('id')
                ->where('premises_card_available', true)
                ->limit(1)
        )
//        ->where('price_total', '<', $filter->priceTotal * 1.2)
//        ->where('price_total', '>=', $filter->priceTotal * 0.8)
        ->where(fn(Builder $builder) => $builder
            ->where($tableName . '.area_total', '>=', $filter->areaTotal - 5)
            ->orWhere($tableName . '.area_total', '<=', $filter->areaTotal + 5))
        ->limit($filter->limit)
        ->orderByDesc('same_type')
        ->orderBy('floor_diff')
        ->orderBy($tableName . '.area_total')
        ->with([
            'status' => static fn(BelongsTo $builder) => $builder->premisesCard(),
            'section' => static fn(BelongsTo $builder) => $builder->premisesCard(),
            'floor' => static fn(BelongsTo $builder) => $builder->premisesCard(),
            'type' => static fn(BelongsTo $builder) => $builder->premisesCard(),
            'features' => static fn(BelongsToMany $builder) => $builder->premisesCard(),
            'planoplan' => static fn(HasOne $builder) => $builder->active()
        ]);

        if ($filter->features) {
            $features = $model->features();

            $query->join(
                $features->getTable(),
                $features->getQualifiedParentKeyName(),
                '=',
                $features->getQualifiedForeignPivotKeyName()
            )->whereIn($features->getTable() . '.feature_id', $filter->features);
        }

        return $this->loadAttachments($query->get(), $this->attachmentRelations());
    }
}
