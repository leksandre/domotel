<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\StepElementAngle;

interface StepElementAngleRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): StepElementAngle;

    public function save(Model $model, null|iterable $masks = null): bool;

    public function getElementsRender(iterable $elementKeys): Collection;
}
