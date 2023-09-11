<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Services\Contracts;

use Kelnik\EstateSearch\Models\Contracts\SearchConfig;

interface SearchConfigFactory
{
    public function make(int|string $primaryKey): ?SearchConfig;
}
