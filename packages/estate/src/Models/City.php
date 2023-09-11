<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $priority
 * @property string $title
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection $districts
 * @property Collection $subwayLines
 */
final class City extends EstateModel
{
    use AsSource;
    use HasFactory;

    protected $table = 'estate_cities';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT
    ];

    protected $fillable = [
        'priority',
        'title',
        'external_id'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $city) {
            $city->districts()->delete();
            $city->subwayLines()->get()->each->delete();
        });
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function subwayLines(): HasMany
    {
        return $this->hasMany(SubwayLine::class);
    }
}
