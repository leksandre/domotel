<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components\Traits;

use Kelnik\Core\View\Components\Contracts\HasMargin;

trait Margin
{
    public static function getMarginVariants(): array
    {
        $res = [];
        $levelPixels = [0, 4, 8, 16, 24, 32, 40, 48, 56, 64, 72, 80];

        for ($i = HasMargin::MARGIN_MIN; $i <= HasMargin::MARGIN_MAX; $i++) {
            $res[$i] = trans(
                'kelnik-core::admin.margin.title',
                [
                    'num' => $i + 1,
                    'px' => $levelPixels[$i] . 'px'
                ]
            );
        }

        return $res;
    }
}
