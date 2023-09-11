<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Planoplan;

use Kelnik\Estate\Models\Planoplan;

final class WidgetFactory
{
    public static function make(Planoplan $planoplan): ?Contracts\Widget
    {
        $versions = config('kelnik-estate.planoplan.widget.classes');

        if (!in_array($planoplan->version, array_keys($versions))) {
//            throw new InvalidArgumentException('Unsupported version ' . $planoplan->version);
            return null;
        }

        $className = $versions[$planoplan->version];

        return new ($className)($planoplan->data);
    }
}
