<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Building;

use Kelnik\Estate\Platform\Filters\BuildingFilter;
use Kelnik\Estate\Platform\Layouts\BaseFilterSelection;
use Orchid\Filters\Filter;

final class FilterSelection extends BaseFilterSelection
{
    /** @return Filter[] */
    public function filters(): array
    {
        return [
            BuildingFilter::class
        ];
    }
}
