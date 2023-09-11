<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

use Orchid\Screen\Fields\Matrix;

/**
 * @method Matrix columns(array $columns)
 * @method Matrix keyValue(bool $keyValue)
 * @method Matrix title(string $value = null)
 * @method Matrix help(string $value = null)
 * @method Matrix sortable(bool $sortable)
 */
class MatrixStatic extends Matrix
{
    protected $view = 'kelnik-core::platform.fields.matrixStatic';

    /** @var array */
    protected $attributes = [
        'index' => 0,
        'removableRows' => false,
        'idPrefix' => null,
        'maxRows' => 0,
        'keyValue' => false,
        'sortable' => false,
        'fields' => [],
        'columns' => [
            'key',
            'value'
        ]
    ];
}
