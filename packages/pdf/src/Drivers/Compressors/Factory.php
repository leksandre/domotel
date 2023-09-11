<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Compressors;

use Kelnik\Pdf\Drivers\Contracts\CompressorDriver;

final class Factory
{
    public const GS = 'gs';
    public const PS2PDF = 'ps2pdf';

    public static function make(string $driverName): CompressorDriver
    {
        $binPath = config('kelnik-pdf.compress.path') ?? '';
        $config = new Config();
        $config->level = config('kelnik-pdf.compress.level');
        $config->binPath = $binPath;

        return match ($driverName) {
            self::GS => new GsDriver($config),
            self::PS2PDF => new Ps2PdfDriver($config)
        };
    }
}
