<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\View\Components\Search;

use Kelnik\Estate\View\Components\Contracts\Template;

final class SearchTemplate extends Template
{
    public string $otherListTemplate = 'kelnik-estate-search::components.search.residential';
}
