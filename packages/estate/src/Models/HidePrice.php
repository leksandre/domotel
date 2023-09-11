<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Orchid\Screen\AsSource;

/**
 * Class HidePrice
 * @package Kelnik\Estate\Models
 *
 * Таблица с настройками скрытия цен для сущностей: Комплекс, корпус, секция
 *
 * @property int $premises_type_id
 * @property int $model_row_id
 * @property string $model_type
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property PremisesType $premisesType
 */
final class HidePrice extends Model
{
    use AsSource;
    use HasFactory;

    protected $table = 'estate_hide_prices';

    protected $fillable = [
        'premises_type_id'
    ];

    public function hidePriceAble(): MorphTo
    {
        return $this->morphTo();
    }

    public function premisesType(): BelongsTo
    {
        return $this->belongsTo(PremisesType::class, 'premises_type_id')->withDefault();
    }
}
