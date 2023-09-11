<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleRepository;

final class StepElementAngleEloquentRepository extends BaseEloquentRepository implements StepElementAngleRepository
{
    protected string $modelNamespace = StepElementAngle::class;

    public function findByPrimary(int|string $primary): StepElementAngle
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    /**
     * @param StepElementAngle $model
     * @param null|iterable $masks
     *
     * @return bool
     */
    public function save(Model $model, ?iterable $masks = null): bool
    {
        $res = $model->save();

        if (!$masks) {
            if (is_array($masks)) {
                $model->masks()->delete();
            }

            return $res;
        }

        $model->masks()->saveMany($masks);

        return $res;
    }

    public function getElementsRender(iterable $elementKeys): Collection
    {
        if (!$elementKeys) {
            return new Collection();
        }

        return $this->modelNamespace::select(['id', 'element_id', 'image_id'])
            ->with(['render'])
            ->whereIn('element_id', $elementKeys)
            ->whereHas('render', static fn(Builder $builder) => $builder->select(['id'])->limit(1))
            ->get()
            ->pluck('render', 'element_id');
    }
}
