<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

final class Range extends \Orchid\Screen\Fields\Range
{
    protected $view = 'kelnik-core::platform.fields.range';

    protected $inlineAttributes = [
        'accesskey',
        'autofocus',
        'disabled',
        'form',
        'name',
        'required',
        'size',
        'tabindex',
        'type',
        'step',
        'max',
        'min',
        'value'
    ];
}
