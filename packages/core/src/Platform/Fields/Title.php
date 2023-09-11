<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

use Orchid\Screen\Fields\Label;

/**
 * Class Title.
 *
 * @method Title name(string $value = null)
 * @method Title popover(string $value = null)
 * @method Title title(string $value = null)
 */
class Title extends Label
{
    /** @var string */
    protected $view = 'kelnik-core::platform.fields.title';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'id'    => null,
        'value' => null,
        'class' => 'kelnik-platform-title'
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'class',
    ];
}
