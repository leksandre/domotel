<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Premises;

use Kelnik\Estate\Platform\Filters\PremisesFilter;
use Kelnik\Estate\Platform\Layouts\BaseFilterSelection;
use Orchid\Filters\Filter;

final class FilterSelection extends BaseFilterSelection
{
    /** @return Filter[] */
    public function filters(): array
    {
        return [
            PremisesFilter::class
        ];
    }
}
