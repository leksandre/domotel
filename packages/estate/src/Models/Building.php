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
use Kelnik\Estate\Database\Factories\BuildingFactory;
use Kelnik\Estate\Models\Contracts\ComplexAble;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Traits\HasHiddenPrices;
use Kelnik\Estate\Models\Traits\HasStat;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $complex_id
 * @property int $completion_id
 * @property int $completion_stage_id
 * @property bool $active
 * @property int $priority
 * @property int $complete_percent
 * @property int $complex_plan_image_id
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
 * @property Complex $complex
 * @property Completion $completion
 * @property CompletionStage $completionStage
 * @property Attachment $complexPlan
 * @property Collection $sections
 * @property-read string $admin_title
 * @property-read bool $completely_active
 * @property-read bool $price_is_visible
 *
 * @method Builder adminList()
 * @method Builder premisesCard()
 */
final class Building extends EstateModel implements ComplexAble
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use HasHiddenPrices;
    use HasStat;

    protected $table = 'estate_buildings';

    protected $attributes = [
        'complex_id' => 0,
        'priority' => self::PRIORITY_DEFAULT,
        'completion_id' => 0,
        'completion_stage_id' => 0,
        'complete_percent' => 0,
        'floor_min' => 0,
        'floor_max' => 0,
        'active' => true
    ];

    protected $fillable = [
        'completion_id',
        'complex_id',
        'completion_stage_id',
        'active',
        'priority',
        'complete_percent',
        'complex_plan_image_id',
        'floor_min',
        'floor_max',
        'slug',
        'title',
        'external_id',
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
        'title',
        'priority',
        'active',
        'created_at',
        'updated_at'
    ];

    protected array $attachmentAttributes = ['complex_plan_image_id'];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $row) {
            $row->hidePrices()->delete();
            $row->sections()->get()->each->delete();
            $row->floors()->get()->each->delete();
        });
    }

    protected static function newFactory(): BuildingFactory
    {
        return BuildingFactory::new();
    }

    public function complex(): BelongsTo
    {
        return $this->belongsTo(Complex::class, 'complex_id')->withDefault();
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function completion(): BelongsTo
    {
        return $this->belongsTo(Completion::class, 'completion_id')->withDefault();
    }

    public function completionStage(): BelongsTo
    {
        return $this->belongsTo(CompletionStage::class, 'completion_stage_id')->withDefault();
    }

    public function complexPlan(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'complex_plan_image_id')->withDefault();
    }

    public function scopeAdminList(Builder $builder): Builder
    {
        return $builder->with([
            'complex' => fn(BelongsTo $b) => $b->select(['id', 'title'])
        ])
            ->select(['id', 'complex_id', 'active', 'external_id', 'title', 'created_at', 'updated_at'])
            ->orderBy('complex_id')
            ->orderBy('priority')
            ->orderBy('title');
    }

    public function scopePremisesCard(Builder $builder): Builder
    {
        return $builder->select([
                'id', 'complex_id', 'complex_plan_image_id', 'completion_id', 'slug', 'title', 'floor_max', 'active'
            ])
            ->with([
                'complex' => fn(BelongsTo $b) => $b->premisesCard(),
                'completion' => fn(BelongsTo $b) => $b->premisesCard()
            ]);
    }

    public function getComplex(): ?Complex
    {
        return $this->relationLoaded('complex') ? $this->complex : null;
    }

    protected function completelyActive(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->active && (!$this->relationLoaded('complex') || $this->complex->active)
        );
    }

    protected function adminTitle(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->complex->title . ' > ' . $this->title
        );
    }

    protected function priceIsVisible(): Attribute
    {
        return Attribute::make(
            get: fn() => true
        );
    }
}
