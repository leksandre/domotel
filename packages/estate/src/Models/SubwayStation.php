<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property SubwayLine $line
 *
 * @method Builder adminList()
 */
final class SubwayStation extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $table = 'estate_subway_stations';

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

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $station) {
            $station->complexes()->detach();
        });
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(SubwayLine::class, 'line_id', 'id')->withDefault();
    }

    public function complexes(): BelongsToMany
    {
        return $this->belongsToMany(
            Complex::class,
            (new ComplexSubwayStationReference())->getTable()
        )->using(ComplexSubwayStationReference::class);
    }

    public function scopeAdminList(Builder $query): Builder
    {
        return $query->with(['line.city' => function ($query) {
            $query->orderBy('priority')->orderBy('title');
        }])
            ->orderBy('priority')
            ->orderBy('title');
    }
}
