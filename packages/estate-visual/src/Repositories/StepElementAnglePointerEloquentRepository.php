<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories;

use Kelnik\EstateVisual\Models\StepElementAnglePointer;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAnglePointerRepository;

final class StepElementAnglePointerEloquentRepository extends BaseEloquentRepository implements
    StepElementAnglePointerRepository
{
    protected string $modelNamespace = StepElementAnglePointer::class;

    public function findByPrimary(int|string $primary): StepElementAnglePointer
    {
        return $this->modelNamespace::findOrNew($primary);
    }
}
