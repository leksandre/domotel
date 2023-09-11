<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Services\Contracts;

use Kelnik\EstateVisual\Models\Contracts\SearchConfig;

interface SearchConfigFactory
{
    public function make(int|string $primaryKey): ?SearchConfig;
}
