<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Kelnik\EstateVisual\Models\Enums\MaskType;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleMaskRepository;

final class StepElementAngleMaskEloquentRepository extends BaseEloquentRepository implements
    StepElementAngleMaskRepository
{
    protected string $modelNamespace = StepElementAngleMask::class;

    public function findByPrimary(int|string $primary): StepElementAngleMask
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getPremisesOnFloorPlan(int|string $premisesPrimaryKey): StepElementAngleMask
    {
        $tableName = (new StepElementAngle())->getTable();
        $subQuery = StepElementAngle::select(['image_id'])->whereColumn('angle_id', $tableName . '.id')->limit(1);
        $mask = $this->modelNamespace::query()
            ->select(['id', 'angle_id', 'coords'])
            ->selectSub($subQuery, 'image_id')
            ->where('estate_premises_id', $premisesPrimaryKey)
            ->where('type', MaskType::Premises->value)
            ->whereHas('angle', function (Builder $builder) {
                $builder->select('id')
                    ->whereHas('element', function (Builder $builder) {
                        $builder->select('id')
                            ->whereHas(
                                'selector',
                                static fn(Builder $query) => $query->select(['id'])->where('active', true)->limit(1)
                            )
                            ->limit(1);
                    })
                    ->limit(1);
            })
            ->orderBy('id')
            ->firstOrNew();

        if ($mask->image_id) {
            $angle = new StepElementAngle(['image_id' => $mask->image_id]);
            $angle->id = $mask->angle_id;
            $angle->load('render');
            $mask->setRelation('angle', $angle);
        }

        return $mask;
    }
}
