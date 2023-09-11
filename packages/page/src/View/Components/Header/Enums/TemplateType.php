<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Header\Enums;

enum TemplateType: string
{
    case Desktop = 'desktop';
    case Mobile = 'mobile';
    case Universal = 'universal';
}
