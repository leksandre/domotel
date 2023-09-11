<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $city_id
 * @property int $priority
 * @property string $title
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property City $city
 *
 * @method Builder adminList()
 */
final class District extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $table = 'estate_districts';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT
    ];

    protected $fillable = [
        'priority',
        'title',
        'external_id'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'created_at',
        'updated_at'
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    public function scopeAdminList(Builder $query): Builder
    {
        return $query->with(['city' => function ($query) {
                $query->orderBy('priority')->orderBy('title');
        }])
            ->orderBy('priority')
            ->orderBy('title');
    }
}
