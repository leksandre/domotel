<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $premises_type_id
 * @property int $model_row_id
 * @property float $value
 * @property string $model_name
 * @property string $name
 *
 * @property Carbon $created_at
 * @property Carbon $modified_at
 */
final class Stat extends EstateModel
{
    use AsSource;
    use HasFactory;

    public const UPDATED_AT = false;

    protected $table = 'estate_stat';

    protected $fillable = [
        'premises_type_id',
        'model_row_id',
        'value',
        'model_name',
        'name'
    ];

    public function hasStatAble(): MorphTo
    {
        return $this->morphTo();
    }
}
