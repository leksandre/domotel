<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Steps;

use Illuminate\Support\Arr;
use Kelnik\Estate\Models\Building;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\Steps\Contracts\Step;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\BuildingRepository;

final class StepBuilding extends Step
{
    protected string $name = Factory::STEP_BUILDING;
    protected ?string $estateModelNamespace = Building::class;

    public function getPriority(): int
    {
        return 10;
    }

    public function getTitle(): string
    {
        return trans('kelnik-estate-visual::steps.building.title');
    }

    public function getAllowedPrev(): array
    {
        return [
            Factory::STEP_COMPLEX
        ];
    }

    public function getAllowedNext(): array
    {
        return [
            Factory::STEP_SECTION,
            Factory::STEP_FLOOR
        ];
    }

    public function associateElementParent(StepElement &$el, ?string $prevStepName, array $estateElByStep): void
    {
        if (empty($el->modelData['complex_id'])) {
            return;
        }

        $el->parent()->associate(
            Arr::get($estateElByStep, Factory::STEP_COMPLEX . '.' . $el->modelData['complex_id'], 0)
        );
    }

    public function getEstateRepository(): BuildingRepository
    {
        return resolve(BuildingRepository::class);
    }
}
