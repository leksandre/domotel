<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Generators;

class Config
{
    public const ORIENTATION_LANDSCAPE = 'landscape';
    public const ORIENTATION_PORTRAIT = 'portrait';

    public const HINTING_NONE = 'none';
    public const HINTING_SLIGHT = 'slight';
    public const HINTING_MEDIUM = 'medium';
    public const HINTING_FULL = 'full';
    public const HINTING_MAX = 'max';

    public const PAGE_WIDTH = 8.5;
    public const PAGE_HEIGHT = 11;

    public const DPI = 600;
    public const IMAGE_QUALITY = 100;

    public int $marginTop = 0;
    public int $marginBottom = 0;
    public int $marginLeft = 0;
    public int $marginRight = 0;

    public int $dpi = self::DPI;
    public int $imageQuality = self::IMAGE_QUALITY;

    public string $pageOrientation = self::ORIENTATION_PORTRAIT;

    /** @var float|int $pageWidth inches */
    public float|int $pageWidth = self::PAGE_WIDTH;

    /** @var float|int $pageHeight inches */
    public float|int $pageHeight = self::PAGE_HEIGHT;

    public bool $printHeaderAndFooter = false;
    public bool $printBackground = true;
    public string $hintingType = self::HINTING_NONE;

    public ?string $binPath = null;
}
