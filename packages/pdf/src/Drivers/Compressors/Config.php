<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Compressors;

class Config
{
    public const LEVEL_SCREEN = 'screen';
    public const LEVEL_EBOOK = 'ebook';
    public const LEVEL_PRINTER = 'printer';
    public const LEVEL_PRE_PRESS = 'prepress';

    public string $level = self::LEVEL_EBOOK;

    public ?string $binPath = null;
}
