<?php

declare(strict_types=1);

namespace Kelnik\Core\Helpers;

final class FileHelper
{
    public static function sizeFormat(int|float $size, int $precision = 2): string
    {
        $units = [
            trans('kelnik-core::filehelper.units.B'),
            trans('kelnik-core::filehelper.units.KB'),
            trans('kelnik-core::filehelper.units.MB'),
            trans('kelnik-core::filehelper.units.GB'),
            trans('kelnik-core::filehelper.units.TB'),
            trans('kelnik-core::filehelper.units.PB'),
        ];

        $unitCount = count($units);

        for ($i = 0; $size > 1024 && ($i + 1 < $unitCount); $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }
}
