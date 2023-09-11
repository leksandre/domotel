<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Models\Contracts\EstateModelReference;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $complex_id
 * @property int $subway_station_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
final class ComplexSubwayStationReference extends EstateModelReference
{
    use AsSource;
    use HasFactory;

    protected $table = 'estate_complex_subway_stations_reference';
}
