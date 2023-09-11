<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Database\Factories\StepElementFactory;
use Kelnik\EstateVisual\Models\Contracts\EstateVisualModel;
use Kelnik\EstateVisual\Models\Traits\ScopeAdminList;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $selector_id
 * @property int $parent_id
 * @property int $estate_model_id
 * @property string $step
 * @property string $estate_model
 * @property string $title
 *
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Selector $selector
 * @property Collection $angles
 * @property Collection $masks
 *
 * @method Builder adminList()
 */
final class StepElement extends EstateVisualModel
{
    use AsSource;
    use HasFactory;
    use ScopeAdminList;

    protected $table = 'estate_visual_step_elements';

    protected $attributes = [
        'selector_id' => 0,
        'parent_id' => 0,
        'estate_model_id' => 0
    ];

    protected $fillable = [
        'active', 'step', 'estate_model', 'title'
    ];

    public array $modelData = [];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $el) {
            $el->angles()->get()->each->delete();
        });
    }

    protected static function newFactory(): StepElementFactory
    {
        return StepElementFactory::new();
    }

    public function selector(): BelongsTo
    {
        return $this->belongsTo(Selector::class, 'selector_id')->withDefault();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id')->withDefault();
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function angles(): HasMany
    {
        return $this->hasMany(StepElementAngle::class, 'element_id');
    }

    public function masks(): HasMany
    {
        return $this->hasMany(StepElementAngle::class, 'angle_id');
    }

    public function scopeByStep(Builder $query, string $stepModel): Builder
    {
        return $query->where('step', $stepModel);
    }
}
