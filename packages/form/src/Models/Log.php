<?php

declare(strict_types=1);

namespace Kelnik\Form\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $form_id
 * @property array $data
 * @property ?Carbon $created_at
 *
 * @property Form $form
 */
final class Log extends Model
{
    use AsSource;
    use Filterable;
    use HasFactory;

    public const UPDATED_AT = null;
    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'form_log';

    protected $attributes = [
        'form_id' => self::DEFAULT_INT_VALUE
    ];

    protected $fillable = [
        'data'
    ];

    protected $casts = [
        'data' => 'encrypted:array'
    ];

    protected array $allowedSorts = [
        'id', 'created_at'
    ];

    protected array $allowedFilters = [
        'id' => Where::class,
        'created_at' => WhereDateStartEnd::class
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id')->withDefault();
    }
}
