<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Generators;

use Kelnik\Pdf\Drivers\Contracts\GeneratorDriver;

final class Factory
{
    public const CDP = 'chrome-dp';
    public const CHROME = 'chrome-cli';
    public const WKHTML = 'wkhtml';

    public static function make(string $driverName): GeneratorDriver
    {
        $config = new Config();
        $connectionConfig = config('kelnik-pdf.connections.' . config('kelnik-pdf.connection'));

        return (match ($driverName) {
            self::CDP => function () use ($config, $connectionConfig) {
                $config->binPath = rtrim($connectionConfig['url'] ?? '', '/');

                return new ChromeDPDriver($config);
            },
            self::CHROME => function () use ($config, $connectionConfig) {
                $config->binPath = $connectionConfig['path'] ?? '';
                $config->hintingType = $connectionConfig['hinting'] ?? $config::HINTING_NONE;

                return new ChromeCliDriver($config);
            },
            self::WKHTML => function () use ($config, $connectionConfig) {
                $config->binPath = $connectionConfig['path'] ?? '';

                return new WkhtmlDriver($config);
            }
        })();
    }
}
