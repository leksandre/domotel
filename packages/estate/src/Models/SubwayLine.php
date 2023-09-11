<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $city_id
 * @property int $priority
 * @property string $title
 * @property string $color
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property City $city
 * @property Collection $stations
 */
final class SubwayLine extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;

    public const COLOR_DEFAULT = '#000';

    protected $table = 'estate_subway_lines';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'color' => self::COLOR_DEFAULT
    ];

    protected $fillable = [
        'priority',
        'title',
        'color',
        'external_id'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'external_id',
        'created_at',
        'updated_at'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $line) {
            $line->stations()->get()->each->delete();
        });
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    public function stations(): HasMany
    {
        return $this->hasMany(SubwayStation::class, 'line_id')->orderBy('priority')->orderBy('title');
    }
}
