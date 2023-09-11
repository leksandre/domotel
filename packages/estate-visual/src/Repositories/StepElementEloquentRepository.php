<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;

final class StepElementEloquentRepository extends BaseEloquentRepository implements StepElementRepository
{
    protected string $modelNamespace = StepElement::class;

    public function findByPrimary(int|string $primary): StepElement
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function findByPrimaryWithAngles(int|string $selectorPrimary, int|string $primary): StepElement
    {
        return $this->modelNamespace::whereKey($primary)
            ->where('selector_id', $selectorPrimary)
            ->with(['angles', 'angles.render', 'angles.masks', 'angles.pointers', 'angles.masks.element'])
            ->orderBy('id')
            ->firstOrNew();
    }

    public function getFirstStepBySelector(int|string $selectorPrimary, ?string $stepName = null): StepElement
    {
        return $this->modelNamespace::where('selector_id', $selectorPrimary)
            ->when($stepName, static fn(Builder $query) => $query->where('step', $stepName))
            ->whereHas('angles', static fn(Builder $query) => $query->select(['id'])->limit(1))
            ->orderBy('parent_id')
            ->orderBy('id')
            ->firstOrNew();
    }

    public function getFirstWithAngles(int|string $selectorPrimary): StepElement
    {
        return $this->modelNamespace::query()
            ->where('selector_id', $selectorPrimary)
            ->with(['angles', 'angles.render', 'angles.masks', 'angles.pointers', 'angles.masks.element'])
            ->orderBy('id')
            ->firstOrNew();
    }

    public function getOtherFloorsByFloor(
        int|string $selectorPrimary,
        int|string $primary,
        int|string $prevStepPrimary,
        ?array $types = []
    ): Collection {
        $step = new StepElement();
        $stepTable = $step->getTable();
        $floor = new Floor();
        $floorTable = $floor->getTable();

        $query = $this->modelNamespace::query()
            ->select($stepTable . '.*', 'fl.number as fl_number')
            ->join($floorTable . ' as fl', 'fl.id', '=', $stepTable . '.estate_model_id')
            ->where($stepTable . '.selector_id', $selectorPrimary)
            ->where($stepTable . '.step', Factory::STEP_FLOOR)
            ->where($stepTable . '.estate_model', Floor::class)
            ->where($stepTable . '.parent_id', $prevStepPrimary)
            ->whereIn(
                'fl.building_id',
                Floor::query()
                    ->select('building_id')
                    ->whereIn(
                        'id',
                        $this->modelNamespace::query()
                            ->select('estate_model_id')
                            ->whereKey($primary)
                            ->where('selector_id', $selectorPrimary)
                            ->where('step', Factory::STEP_FLOOR)
                            ->where('estate_model', Floor::class)
                    )
            )
            ->orderBy('fl.priority')
            ->orderBy('fl.number');

        if ($types) {
            $query->whereIn(
                'fl.id',
                Premises::query()
                    ->select('floor_id')
                    ->whereHas(
                        'type',
                        static fn(Builder $builder) => $builder->select(['id'])->whereIn('group_id', $types)
                    )
            );
        }

        return $query->get();
    }

    public function getPrevSteps(
        int|string $selectorPrimary,
        int|string $stepElementPrimary,
        array $prevSteps
    ): Collection {
        $query = $this->modelNamespace::query()
            ->where('selector_id', $selectorPrimary)
            ->whereHas('angles', function (Builder $builder) use ($stepElementPrimary, $prevSteps) {
                $builder->select(['id'])
                    ->whereHas('masks', function (Builder $subBuilder) use ($stepElementPrimary, $prevSteps) {
                        $subBuilder->select(['id'])
                            ->where('element_id', $stepElementPrimary)
                            ->orWhereIn('type', $prevSteps)
                            ->limit(1);
                    })
                    ->limit(1);
            });

        return $query->get()->pluck(null, 'step');
    }
}
