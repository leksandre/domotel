<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components\Contracts;

interface HasMargin
{
    public const MARGIN_MIN = 0;
    public const MARGIN_MAX = 11;
    public const MARGIN_DEFAULT = 7;

    public static function getMarginVariants(): array;
}
