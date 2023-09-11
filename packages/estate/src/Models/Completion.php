<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Kelnik\Estate\Database\Factories\CompletionFactory;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Traits\ScopeAdminList;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $priority
 * @property ?Carbon $event_date
 * @property string $title
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection $stages
 * @property Collection $complexes
 * @property Collection $buildings
 *
 * @method Builder adminList()
 * @method Builder premisesCard()
 */
final class Completion extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use ScopeAdminList;

    protected $table = 'estate_completions';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT
    ];

    protected $casts = [
        'event_date' => 'date'
    ];

    protected $fillable = [
        'priority',
        'event_date',
        'title',
        'external_id'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'event_date',
        'title',
        'created_at',
        'updated_at'
    ];

    protected static function newFactory(): CompletionFactory
    {
        return CompletionFactory::new();
    }

    public function stages(): HasMany
    {
        return $this->hasMany(CompletionStage::class);
    }

    public function complexes(): HasMany
    {
        return $this->hasMany(Complex::class, 'completion_id');
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'completion_id');
    }

    public function scopePremisesCard(Builder $builder): Builder
    {
        return $builder->select(['id', 'event_date', 'title']);
    }
}
