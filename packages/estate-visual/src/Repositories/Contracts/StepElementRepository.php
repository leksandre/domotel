<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\StepElement;

interface StepElementRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): StepElement;

    public function findByPrimaryWithAngles(int|string $selectorPrimary, int|string $primary): StepElement;

    public function getFirstStepBySelector(int|string $selectorPrimary, ?string $stepName = null): StepElement;

    public function getFirstWithAngles(int|string $selectorPrimary): StepElement;

    public function getOtherFloorsByFloor(
        int|string $selectorPrimary,
        int|string $primary,
        int|string $prevStepPrimary,
        ?array $types = []
    ): Collection;

    public function getPrevSteps(
        int|string $selectorPrimary,
        int|string $stepElementPrimary,
        array $prevSteps
    ): Collection;
}
