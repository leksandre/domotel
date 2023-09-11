<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\Premises;

interface SearchRepository
{
    public function getPriceValues(Collection $filters): Premises;

    public function getRoomValues(Collection $filters): Collection;

    public function getPremises(Collection $filters, array $primaryKeys): Collection|LazyCollection;

    public function getFloorsWithPremisesCount(Collection $filters): Collection;

    public function getFloorIdsWithPremisesStat(Collection $filters): Collection;

    public function getSectionIdsWithPremisesStat(Collection $filters): Collection;

    public function getBuildingIdsWithPremisesStat(Collection $filters): Collection;
}
