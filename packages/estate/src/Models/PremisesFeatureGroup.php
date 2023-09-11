<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Database\Factories\PremisesFeatureGroupFactory;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Traits\ScopeActive;
use Kelnik\Estate\Models\Traits\ScopeAdminList;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $priority
 * @property bool $active
 * @property bool $general
 * @property string $title
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection $features
 *
 * @method Builder adminList()
 * @method Builder active()
 */
final class PremisesFeatureGroup extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use ScopeAdminList;
    use ScopeActive;

    public const CACHE_GENERAL_GROUP_ID = 'estate_premises_feature_general_group';

    protected $table = 'estate_premises_feature_groups';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'active' => true,
        'general' => false
    ];

    protected $fillable = [
        'priority',
        'title',
        'active',
        'general',
        'external_id'
    ];

    protected $casts = [
        'active' => 'boolean',
        'general' => 'boolean'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'title',
        'active',
        'general',
        'external_id',
        'created_at',
        'updated_at'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::saved(function (self $row) {
            if ($row->isDirty('general') && $row->general) {
                self::withoutEvents(fn() => self::query()->whereKeyNot($row->getKey())->update(['general' => false]));
            }
        });

        self::deleted(function (self $row) {
            $row->features()->get()->each->delete();
        });
    }

    protected static function newFactory(): PremisesFeatureGroupFactory
    {
        return PremisesFeatureGroupFactory::new();
    }

    public function features(): HasMany
    {
        return $this->hasMany(PremisesFeature::class, 'group_id')->orderBy('priority')->orderBy('title');
    }
}
