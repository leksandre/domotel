<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractCheckboxFilter;

final class Building extends AbstractCheckboxFilter
{
    public const NAME = 'blocks';
    protected const FILTER_FIELD = 'floor.building_id';
    protected const TITLE = 'kelnik-estate-search::front.form.building.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.building';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);
        $values = $this->repository->getBuildingValues(
            $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
        );

        if (!$values) {
            return $res;
        }

        $newValues = [];

        /** @var \Kelnik\Estate\Models\Building $building */
        foreach ($values as $building) {
            if (is_numeric($building->title)) {
                $building->title = trans(self::TITLE) . ' ' . $building->title;
            }
            $newValues[$building->getKey()] = $building->toArray();
        }

        $res->put('values', $newValues);
        unset($values, $newValues);

        return $res;
    }
}
