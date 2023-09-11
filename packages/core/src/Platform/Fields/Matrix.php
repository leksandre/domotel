<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

/**
 * Class Matrix.
 *
 * @method Matrix columns(array $columns)
 * @method Matrix keyValue(bool $keyValue)
 * @method Matrix title(string $value = null)
 * @method Matrix help(string $value = null)
 * @method Matrix sortable(bool $sortable)
 */
class Matrix extends \Orchid\Screen\Fields\Matrix
{
    /** @var string */
    protected $view = 'kelnik-core::platform.fields.matrix';

    /** @var array */
    protected $attributes = [
        'index' => 0,
        'removableRows' => true,
        'idPrefix' => null,
        'maxRows' => 0,
        'keyValue' => false,
        'sortable' => false,
        'fields' => [],
        'columns' => [
            'key',
            'value',
        ],
    ];
}
