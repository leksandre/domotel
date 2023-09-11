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
use Kelnik\Estate\Database\Factories\FloorFactory;
use Kelnik\Estate\Models\Contracts\ComplexAble;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $building_id
 * @property bool $active
 * @property int $priority
 * @property int $number
 * @property string $slug
 * @property string $external_id
 * @property string $hash
 * @property string $title
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Building $building
 * @property Collection $premises
 * @property-read string $admin_title
 * @property-read bool $completely_active
 * @property-read bool $price_is_visible
 *
 * @method Builder adminList()
 * @method Builder premisesCard()
 */
final class Floor extends EstateModel implements ComplexAble
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $table = 'estate_floors';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'building_id' => 0,
        'active' => true,
        'number' => 0
    ];

    protected $fillable = [
        'building_id',
        'active',
        'priority',
        'number',
        'slug',
        'title',
        'external_id',
        'hash'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected array $allowedFilters = [
        'id' => Where::class,
        'title' => Like::class,
        'number' =>  Where::class,
        'external_id' => Like::class
    ];

    protected array $allowedSorts = [
        'title',
        'priority',
        'number',
        'active',
        'created_at',
        'updated_at'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $row) {
            $row->premises()->get()->each->delete();
        });
    }

    protected static function newFactory(): FloorFactory
    {
        return FloorFactory::new();
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
        return $builder->with(['building' => function (BelongsTo $query) {
            $query->adminList();
        }])
            ->select(['id', 'building_id', 'active', 'external_id', 'title', 'created_at', 'updated_at'])
            ->orderBy('building_id')
            ->orderBy('priority')
            ->orderBy('title');
    }

    public function scopePremisesCard(Builder $builder): Builder
    {
        return $builder->select(['id', 'building_id', 'slug', 'title', 'number', 'active'])
            ->with([
                'building' => fn(BelongsTo $b) => $b->premisesCard()
            ]);
    }

    public function getComplex(): ?EstateModel
    {
        return $this->relationLoaded('building') ? $this->building->getComplex() : null;
    }

    protected function completelyActive(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->active && (!$this->relationLoaded('building') || $this->building->completely_active)
        );
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
