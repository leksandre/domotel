<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Header\Enums;

enum Style: string
{
    case Fixed = 'fixed';
    case FixedTransparent = 'fixedTransparent';
    case White = 'white';
    case Transparent = 'transparent';
    case Adaptive = 'adaptive';
    case AdaptiveTrans = 'adaptiveTrans';

    public function title(): string
    {
        return trans('kelnik-page::admin.components.header.style.' . $this->value);
    }
}
