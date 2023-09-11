<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Platform\Fields;

use Orchid\Screen\Fields\Matrix;

/**
 * @method Variants columns(array $columns)
 * @method Variants keyValue(bool $keyValue)
 * @method Variants title(string $value = null)
 * @method Variants help(string $value = null)
 * @method Variants sortable(bool $sortable)
 */
final class Variants extends Matrix
{
    /** @var string */
    protected $view = 'kelnik-mortgage::platform.fields.variants';

    /** @var array<string, mixed> */
    protected $attributes = [
        'index' => 0,
        'idPrefix' => null,
        'maxRows' => 0,
        'keyValue' => false,
        'sortable' => false,
        'fields' => [],
        'columns' => ['key', 'value',],
    ];
}
