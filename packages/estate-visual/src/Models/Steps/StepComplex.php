<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Steps;

use Kelnik\Estate\Models\Complex;
use Kelnik\EstateVisual\Models\Steps\Contracts\Step;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\ComplexRepository;

final class StepComplex extends Step
{
    protected string $name = Factory::STEP_COMPLEX;
    protected ?string $estateModelNamespace = Complex::class;

    public function getLink(): ?string
    {
        return '/';
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function getTitle(): string
    {
        return trans('kelnik-estate-visual::steps.complex.title');
    }

    public function getAllowedPrev(): array
    {
        return [];
    }

    public function getAllowedNext(): array
    {
        return [
            Factory::STEP_BUILDING,
            Factory::STEP_SECTION,
            Factory::STEP_FLOOR
        ];
    }

    public function associateElementParent(StepElement &$el, ?string $prevStepName, array $estateElByStep): void
    {
    }

    public function getEstateRepository(): ComplexRepository
    {
        return resolve(ComplexRepository::class);
    }
}
