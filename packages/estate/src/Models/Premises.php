<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Database\Factories\PremisesFactory;
use Kelnik\Estate\Models\Contracts\ComplexAble;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Traits\ScopeActive;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;
use Orchid\Attachment\Models\Attachment;
use Orchid\Attachment\Models\Attachmentable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $type_id
 * @property int $original_type_id
 * @property int $status_id
 * @property int $original_status_id
 * @property int $floor_id
 * @property int $section_id
 * @property int $plan_type_id
 * @property bool $active
 * @property bool $action
 * @property int $rooms
 * @property float $price
 * @property float $price_total
 * @property float $price_sale
 * @property float $price_meter
 * @property float $price_rent
 * @property float $area_total
 * @property float $area_living
 * @property float $area_kitchen
 * @property ?int $image_list_id
 * @property ?int $image_plan_id
 * @property ?int $image_plan_furniture_id
 * @property ?int $image3d_id
 * @property ?int $image_on_floor_id
 * @property int $number_on_floor
 * @property string $external_id
 * @property string $hash
 * @property string $number
 * @property string $plan_type_string
 * @property string $title
 * @property string $planoplan_code
 * @property string $vr_link
 * @property array $additional_properties
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property PremisesType $type
 * @property PremisesType $originalType
 * @property PremisesStatus $status
 * @property PremisesStatus $originalStatus
 * @property Floor $floor
 * @property Section $section
 * @property PremisesPlanType $planType
 * @property Collection $features
 * @property Attachment $imageList
 * @property Attachment $imagePlan
 * @property Attachment $imagePlanFurniture
 * @property Attachment $image3D
 * @property Attachment $imageOnFloor
 * @property Planoplan $planoplan
 * @property Collection $gallery
 * @property-read ?int $floor_max
 * @property-read bool $completely_active
 * @property-read bool $price_is_visible
 * @property-read float $discount
 *
 * @method Builder adminList()
 * @method Builder active()
 */
final class Premises extends EstateModel implements ComplexAble
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use ScopeActive;

    public const ROOMS_DEFAULT = 0;
    public const PRICE_DEFAULT = 0.00;
    public const AREA_DEFAULT = 0.00;

    public const NUMBER_MAX_LENGTH = 100;

    protected $table = 'estate_premises';

    protected $fillable = [
        'type_id',
        'original_type_id',
        'status_id',
        'original_status_id',
        'floor_id',
        'section_id',
        'plan_type_id',
        'active',
        'action',
        'rooms',
        'price',
        'price_total',
        'price_sale',
        'price_meter',
        'price_rent',
        'area_total',
        'area_living',
        'area_kitchen',
        'image_list_id',
        'image_plan_id',
        'image_plan_furniture_id',
        'image_3d_id',
        'image_on_floor_id',
        'number_on_floor',
        'external_id',
        'hash',
        'plan_type_string',
        'number',
        'title',
        'planoplan_code',
        'vr_link',
        'additional_properties'
    ];

    protected $casts = [
        'active' => 'boolean',
        'action' => 'boolean',
        'additional_properties' => 'array',
        'price' => 'float',
        'price_total' => 'float',
        'price_sale' => 'float',
        'price_meter' => 'float',
        'price_rent' => 'float',
        'area_total' => 'float',
        'area_living' => 'float',
        'area_kitchen' => 'float'
    ];

    protected $attributes = [
        'type_id' => 0,
        'original_type_id' => 0,
        'status_id' => 0,
        'original_status_id' => 0,
        'floor_id' => 0,
        'section_id' => 0,
        'plan_type_id' => 0,
        'active' => true,
        'action' => false,
        'rooms' => self::ROOMS_DEFAULT,
        'price' => self::PRICE_DEFAULT,
        'price_total' => self::PRICE_DEFAULT,
        'price_sale' => self::PRICE_DEFAULT,
        'price_meter' => self::PRICE_DEFAULT,
        'price_rent' => self::PRICE_DEFAULT,
        'area_total' => self::AREA_DEFAULT,
        'area_living' => self::AREA_DEFAULT,
        'area_kitchen' => self::AREA_DEFAULT
    ];

    protected array $allowedSorts = [
        'id',
        'title',
        'number',
        'number_on_floor',
        'floor_id',
        'section_id',
        'status_id',
        'type_id',
        'active',
        'action',
        'external_id'
    ];

    protected array $allowedFilters = [
        'title' => Like::class,
        'number' => Like::class,
        'number_on_floor' => Where::class
    ];

    protected array $attachmentAttributes = [
        'image_list_id',
        'image_plan_id',
        'image_plan_furniture_id',
        'image_3d_id',
        'image_on_floor_id'
    ];

    public ?string $typeTitle = null;
    public ?string $typeShortTitle = null;
    public ?string $imagePlanDefault = null;
    public ?string $imagePlanPicture = null;
    public ?string $image3dPicture = null;
    public ?string $imageOnFloorPicture = null;
    public ?string $imageBuildingPlanPicture = null;
    public ?string $url = null;
    public ?string $routeName = null;

    protected static function boot(): void
    {
        parent::boot();

        self::created(function (self $row) {
            if (!$row->planoplan_code) {
                return;
            }

            $row->planoplan()->updateOrCreate(['id' => $row->planoplan_code]);
        });

        self::updated(function (self $row) {
            if ($row->isClean('planoplan_code')) {
                return;
            }

            if ($row->planoplan_code) {
                $row->planoplan()->updateOrCreate(['id' => $row->planoplan_code]);
            }

            if (!$row->getOriginal('planoplan_code')) {
                return;
            }

            $planoplan = resolve(PlanoplanRepository::class)->findByPrimary(
                $row->getOriginal('planoplan_code')
            );

            if ($planoplan->exists) {
                $planoplan->delete();
            }
        });

        self::deleted(function (self $row) {
            $row->features()->detach();
            $row->gallery()->get()->each->delete();

            if ($row->planoplan_code) {
                $row->planoplan()->get()?->delete();
            }
        });
    }

    protected static function newFactory(): PremisesFactory
    {
        return PremisesFactory::new();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PremisesType::class, 'type_id')->withDefault();
    }

    public function originalType(): BelongsTo
    {
        return $this->belongsTo(PremisesType::class, 'original_type_id')->withDefault();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(PremisesStatus::class, 'status_id')->withDefault();
    }

    public function originalStatus(): BelongsTo
    {
        return $this->belongsTo(PremisesStatus::class, 'original_status_id')->withDefault();
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class)->withDefault();
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class)->withDefault();
    }

    public function planType(): BelongsTo
    {
        return $this->belongsTo(PremisesPlanType::class)->withDefault();
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            PremisesFeature::class,
            (new PremisesFeatureReference())->getTable(),
            'premises_id',
            'feature_id'
        )->using(PremisesFeatureReference::class)->withTimestamps();
    }

    public function imageList(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_search_id')->withDefault();
    }

    public function imagePlan(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_plan_id')->withDefault();
    }

    public function imagePlanFurniture(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_plan_furniture_id')->withDefault();
    }

    public function image3D(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_3d_id')->withDefault();
    }

    public function imageOnFloor(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_on_floor_id')->withDefault();
    }

    public function planoplan(): HasOne
    {
        return $this->hasOne(Planoplan::class, 'id', 'planoplan_code')->withDefault();
    }

    public function gallery(): MorphToMany
    {
        return $this->morphToMany(
            Attachment::class,
            'attachmentable',
            (new Attachmentable())->getTable(),
            'attachmentable_id',
            'attachment_id'
        )->orderBy('sort');
    }

    public function getComplex(): ?EstateModel
    {
        return $this->relationLoaded('floor') ? $this->floor->getComplex() : null;
    }

    protected function completelyActive(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->active
                && (!$this->relationLoaded('section') || $this->section->active)
                && (!$this->relationLoaded('floor') || $this->floor->completely_active)
        );
    }

    protected function floorMax(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('section') && $this->section->exists && $this->section->floor_max) {
                    return $this->section->floor_max;
                } elseif (
                    $this->relationLoaded('floor')
                    && $this->floor->relationLoaded('building')
                    && $this->floor->building->exists
                ) {
                    return $this->floor->building->floor_max ?: null;
                }

                return null;
            }
        );
    }

    protected function priceIsVisible(): Attribute
    {
        return Attribute::make(
            get: function () {
                return !(
                    ($this->relationLoaded('status') && !$this->status->price_is_visible)
                    || ($this->relationLoaded('section') && !$this->section->price_is_visible)
                    || ($this->relationLoaded('floor') && !$this->floor->price_is_visible)
                );
            }
        );
    }

    protected function discount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->price_sale ? $this->price_total - $this->price_sale : 0
        );
    }
}
