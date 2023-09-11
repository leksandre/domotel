<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;

interface SearchRepository
{
    public function getPriceValues(Collection $filters): Premises;

    public function getAreaValues(Collection $filters): Premises;

    public function getTypeValues(Collection $filters): Collection;

    public function getTypesByGroup(array $typeGroupIds): Collection;

    public function getStatusValues(Collection $filters, array $allowStatuses): Collection;

    public function getFloorValues(Collection $filters): Floor;

    public function getBuildingValues(Collection $filters): Collection;

    public function getSectionValues(Collection $filters): Collection;

    public function getCompletionValues(Collection $filters): Collection;

    public function getFeatureValues(Collection $filters): Collection;

    public function hasPromotionalPremises(Collection $filters): bool;

    public function hasPremisesByFilter(Collection $filters): bool;

    public function getCount(Collection $filters): int;

    public function getResults(
        Collection $filters,
        Collection $orders,
        int $limit = 0,
        int $offset = 0
    ): array|Collection|LazyCollection;
}
