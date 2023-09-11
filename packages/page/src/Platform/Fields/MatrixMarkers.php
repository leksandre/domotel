<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Fields;

use Orchid\Screen\Fields\Matrix;

/**
 * Class MarkersMatrix.
 *
 * @method MatrixMarkers columns(array $columns)
 * @method MatrixMarkers keyValue(bool $keyValue)
 * @method MatrixMarkers title(string $value = null)
 * @method MatrixMarkers help(string $value = null)
 * @method MatrixMarkers markerType(string $value)
 */
final class MatrixMarkers extends Matrix
{
    /**
     * @var string
     */
    protected $view = 'kelnik-page::platform.fields.matrixMarkers';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'index'    => 0,
        'idPrefix' => null,
        'maxRows'  => 0,
        'keyValue' => false,
        'markerType' => '',
        'fields'   => [],
        'columns'  => [
            'key',
            'value',
        ],
    ];
}
