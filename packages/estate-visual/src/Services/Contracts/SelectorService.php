<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Services\Contracts;

use Kelnik\EstateVisual\Models\Selector;

interface SelectorService
{
    public static function getCacheTag(Selector $selector): string;
}
