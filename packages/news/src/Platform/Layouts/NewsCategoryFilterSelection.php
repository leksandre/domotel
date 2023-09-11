<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts;

use Kelnik\News\Platform\Filters\NewsCategoryFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

final class NewsCategoryFilterSelection extends Selection
{
    /** @return Filter[] */
    public function filters(): array
    {
        return [
            NewsCategoryFilter::class
        ];
    }
}
