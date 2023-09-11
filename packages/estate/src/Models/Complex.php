<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Core\Map\Contracts\Coords;
use Kelnik\Estate\Database\Factories\ComplexFactory;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Traits\HasHiddenPrices;
use Kelnik\Estate\Models\Traits\HasStat;
use Kelnik\Estate\Models\Traits\ScopeAdminList;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $type_id
 * @property int $status_id
 * @property int $district_id
 * @property int $completion_id
 * @property int $completion_stage_id
 * @property bool $active
 * @property bool $show_custom_prices
 * @property int $priority
 * @property int $cover_image_id
 * @property int $logo_image_id
 * @property int $map_marker_image_id
 * @property int $floor_min
 * @property int $floor_max
 * @property int $map_zoom
 * @property string $slug
 * @property string $external_id
 * @property string $hash
 * @property string $title
 * @property string $type_description
 * @property string $address
 * @property string $site_url
 * @property Coords $map_coords
 * @property Coords $map_center_coords
 * @property Collection $options
 * @property Collection $custom_prices
 * @property string $description
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property ComplexType $type
 * @property ComplexStatus $status
 * @property District $district
 * @property Completion $completion
 * @property CompletionStage $completionStage
 * @property Attachment $cover
 * @property Attachment $logo
 * @property Attachment $mapMarker
 * @property Collection $buildings
 * @property Collection $subwayStations
 * @property Collection $premisesPlanTypes
 * @property-read bool $price_is_visible
 *
 * @method Builder adminList()
 * @method Builder premisesCard()
 */
final class Complex extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use HasHiddenPrices;
    use HasStat;
    use ScopeAdminList;

    protected $table = 'estate_complexes';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'type_id' => 0,
        'status_id' => 0,
        'district_id' => 0,
        'completion_id' => 0,
        'completion_stage_id' => 0,
        'floor_min' => 0,
        'floor_max' => 0,
        'active' => false,
        'show_custom_prices' => false
    ];

    protected $fillable = [
        'type_id',
        'status_id',
        'district_id',
        'completion_id',
        'completion_stage_id',
        'active',
        'show_custom_prices',
        'priority',
        'cover_image_id',
        'logo_image_id',
        'map_marker_image_id',
        'floor_min',
        'floor_max',
        'map_zoom',
        'slug',
        'external_id',
        'hash',
        'title',
        'type_description',
        'address',
        'site_url',
        'map_coords',
        'map_center_coords',
        'options',
        'custom_prices',
        'description'
    ];

    protected $casts = [
        'active' => 'boolean',
        'show_custom_prices' => 'boolean',
        'map_coords' => 'array',
        'map_center_coords' => 'array',
        'options' => 'collection',
        'custom_prices' => 'collection'
    ];

    protected array $allowedFilters = [
        'title' => Like::class,
        'external_id' => Like::class
    ];

    protected array $allowedSorts = [
        'id',
        'title',
        'active',
        'priority',
        'created_at',
        'updated_at'
    ];

    protected array $attachmentAttributes = [
        'cover_image_id',
        'logo_image_id',
        'map_marker_image_id'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $complex) {
            $complex->subwayStations()->detach();
            $complex->hidePrices()->delete();
            $complex->stat()->delete();
            $complex->premisesPlanTypes()->get()->each->delete();
            $complex->buildings()->get()->each->delete();
        });
    }

    protected static function newFactory(): ComplexFactory
    {
        return ComplexFactory::new();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ComplexType::class, 'id', 'type_id')->withDefault();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ComplexStatus::class, 'id', 'status_id')->withDefault();
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class)->withDefault();
    }

    public function completion(): BelongsTo
    {
        return $this->belongsTo(Completion::class)->withDefault();
    }

    public function completionStage(): BelongsTo
    {
        return $this->belongsTo(CompletionStage::class)->withDefault();
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'cover_image_id')->withDefault();
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'logo_image_id')->withDefault();
    }

    public function mapMarker(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'map_marker_image_id')->withDefault();
    }

    public function subwayStations(): BelongsToMany
    {
        return $this->belongsToMany(
            SubwayStation::class,
            (new ComplexSubwayStationReference())->getTable()
        )->using(ComplexSubwayStationReference::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'complex_id');
    }

    public function premisesPlanTypes(): HasMany
    {
        return $this->hasMany(PremisesPlanType::class, 'complex_id');
    }

    public function scopePremisesCard(Builder $builder): Builder
    {
        return $builder->select(['id', 'slug', 'title', 'active']);
    }

    protected function priceIsVisible(): Attribute
    {
        return Attribute::make(
            get: fn() => true
        );
    }
}
