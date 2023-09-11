<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Steps;

use Illuminate\Support\Arr;
use Kelnik\Estate\Models\Section;
use Kelnik\EstateVisual\Models\Steps\Contracts\Step;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\SectionRepository;

final class StepSection extends Step
{
    protected string $name = Factory::STEP_SECTION;
    protected ?string $estateModelNamespace = Section::class;

    public function getPriority(): int
    {
        return 20;
    }

    public function getTitle(): string
    {
        return trans('kelnik-estate-visual::steps.section.title');
    }

    public function getAllowedPrev(): array
    {
        return [
            Factory::STEP_COMPLEX,
            Factory::STEP_BUILDING
        ];
    }

    public function getAllowedNext(): array
    {
        return [
            Factory::STEP_FLOOR
        ];
    }

    public function getSectionTitle(): ?string
    {
        return trans('kelnik-estate-visual::steps.building.title');
    }

    public function associateElementParent(StepElement &$el, ?string $prevStepName, array $estateElByStep): void
    {
        $stepName = Factory::STEP_BUILDING;
        $column = 'building_id';

        if ($prevStepName === Factory::STEP_COMPLEX) {
            $stepName = $prevStepName;
            $column = 'complex_id';
        }

        if (empty($el->modelData[$column])) {
            return;
        }

        $el->parent()->associate(
            Arr::get($estateElByStep, $stepName . '.' . $el->modelData[$column], 0)
        );
    }

    public function getEstateRepository(): SectionRepository
    {
        return resolve(SectionRepository::class);
    }

    public function adminListAsAccordion(): bool
    {
        return true;
    }
}
