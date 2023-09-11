<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Services;

use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Providers\EstateVisualServiceProvider;

final class SelectorService implements Contracts\SelectorService
{
    private const CACHE_TAG = EstateVisualServiceProvider::MODULE_NAME;

    public static function getCacheTag(Selector $selector): string
    {
        return self::CACHE_TAG . '_' . $selector->getKey();
    }
}
