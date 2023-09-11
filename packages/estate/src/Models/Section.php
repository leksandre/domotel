<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Database\Factories\SectionFactory;
use Kelnik\Estate\Models\Contracts\ComplexAble;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Traits\HasHiddenPrices;
use Kelnik\Estate\Models\Traits\HasStat;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $building_id
 * @property bool $active
 * @property int $priority
 * @property int $floor_min
 * @property int $floor_max
 * @property string $slug
 * @property string $external_id
 * @property string $hash
 * @property string $title
 * @property string $description
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Building $building
 * @property Collection $premises
 * @property-read string $admin_title
 * @property-read bool $price_is_visible
 *
 * @method Builder adminList()
 * @method Builder premisesCard()
 */
final class Section extends EstateModel implements ComplexAble
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use HasHiddenPrices;
    use HasStat;

    protected $table = 'estate_sections';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'floor_min' => 0,
        'floor_max' => 0,
        'building_id' => 0,
        'active' => true
    ];

    protected $fillable = [
        'active',
        'priority',
        'floor_min',
        'floor_max',
        'slug',
        'title',
        'external_id',
        'building_id',
        'hash',
        'description'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected array $allowedFilters = [
        'id' => Where::class,
        'title' => Like::class,
        'external_id' => Like::class
    ];

    protected array $allowedSorts = [
        'id',
        'title',
        'priority',
        'active',
        'created_at',
        'updated_at'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $row) {
            $row->hidePrices()->delete();
        });
    }

    protected static function newFactory(): SectionFactory
    {
        return SectionFactory::new();
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class)->withDefault();
    }

    public function premises(): HasMany
    {
        return $this->hasMany(Premises::class);
    }

    public function scopeAdminList(Builder $builder): Builder
    {
        return $builder->with([
            'building' => fn(BelongsTo $b) => $b->adminList()
        ])
            ->select(['id', 'building_id', 'active', 'external_id', 'title', 'created_at', 'updated_at'])
            ->orderBy('building_id')
            ->orderBy('priority')
            ->orderBy('title');
    }

    public function scopePremisesCard(Builder $builder): Builder
    {
        return $builder->select(['id', 'title', 'floor_max', 'active']);
    }

    public function getComplex(): ?EstateModel
    {
        return $this->relationLoaded('building') ? $this->building->getComplex() : null;
    }

    protected function adminTitle(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->building->admin_title . ' > ' . $this->title
        );
    }

    protected function priceIsVisible(): Attribute
    {
        return Attribute::make(
            get: fn() => true
        );
    }
}
