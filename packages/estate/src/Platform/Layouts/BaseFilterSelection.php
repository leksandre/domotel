<?php

namespace Kelnik\Estate\Platform\Layouts;

use Orchid\Screen\Layouts\Selection;

abstract class BaseFilterSelection extends Selection
{
    public const TEMPLATE_LINE = 'kelnik-estate::platform.layouts.filter';
    public $template = self::TEMPLATE_LINE;
}
