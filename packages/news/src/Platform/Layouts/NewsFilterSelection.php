<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts;

use Kelnik\News\Platform\Filters\NewsFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

final class NewsFilterSelection extends Selection
{
    /** @return Filter[] */
    public function filters(): array
    {
        return [
            NewsFilter::class
        ];
    }
}
