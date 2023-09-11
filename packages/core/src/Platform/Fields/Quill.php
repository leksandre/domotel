<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

class Quill extends \Orchid\Screen\Fields\Quill
{
    /** @var string */
    protected $view = 'kelnik-core::platform.fields.quill';

    /** @var array */
    protected $attributes = [
        'value'   => null,
        'toolbar' => ['text', 'color', 'quote', 'header', 'list', 'format', 'media', 'kelnik'],
        'height'  => '300px',
        'base64'  => false
    ];
}
