<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kelnik\Estate\Models\Complex;
use Kelnik\EstateVisual\Database\Factories\SelectorFactory;
use Kelnik\EstateVisual\Models\Contracts\EstateVisualModel;
use Kelnik\EstateVisual\Models\Traits\ScopeAdminList;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $complex_id
 * @property bool $active
 * @property string $external_id
 * @property string $title
 * @property array $steps
 * @property Collection $settings
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Complex $complex
 * @property Collection $stepElements
 *
 * @method Builder adminList()
 */
final class Selector extends EstateVisualModel
{
    use AsSource;
    use HasFactory;
    use ScopeAdminList;

    protected $table = 'estate_visual_selectors';

    protected $attributes = [
        'complex_id' => 0,
        'active' => false
    ];

    protected $fillable = [
        'active', 'external_id', 'steps', 'settings', 'title'
    ];

    protected $casts = [
        'active' => 'boolean',
        'steps' => 'array',
        'settings' => 'collection'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (self $selector) {
            $selector->external_id = Str::uuid();
        });

        self::deleted(function (self $selector) {
            $selector->stepElements()->get()->each->delete();
        });
    }

    protected static function newFactory(): SelectorFactory
    {
        return SelectorFactory::new();
    }

    public function complex(): BelongsTo
    {
        return $this->belongsTo(Complex::class, 'complex_id')->withDefault();
    }

    public function stepElements(): HasMany
    {
        return $this->hasMany(StepElement::class, 'selector_id');
    }

    public function stepIsAllow(string $step): bool
    {
        return in_array($step, $this->steps);
    }
}
