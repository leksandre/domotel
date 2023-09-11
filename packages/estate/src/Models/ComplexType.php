<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $priority
 * @property string $title
 * @property string $title_short
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
final class ComplexType extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $table = 'estate_complex_types';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT
    ];

    protected $fillable = [
        'priority',
        'title',
        'title_short',
        'external_id'
    ];
}
