<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Database\Factories\PremisesFeatureFactory;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Traits\ScopeActive;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureGroupRepository;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $group_id
 * @property int $priority
 * @property bool $active
 * @property ?int $icon_id
 * @property string $title
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property PremisesFeatureGroup $featureGroup
 * @property Attachment $icon
 * @property Collection $premises
 *
 * @property-read string $admin_title
 * @property-read string $full_title
 * @method Builder adminList()
 * @method Builder premisesCard()
 */
final class PremisesFeature extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use ScopeActive;

    protected $table = 'estate_premises_features';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'active' => true
    ];

    protected $fillable = [
        'priority',
        'title',
        'active',
        'icon_id',
        'external_id'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'title',
        'active',
        'external_id',
        'created_at',
        'updated_at'
    ];

    protected array $attachmentAttributes = [
        'icon_id'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (self $row) {
            if (!$row->isClean('group_id')) {
                return;
            }

            $row->group_id = resolve(PremisesFeatureGroupRepository::class)->getGeneralKey();
        });

        self::deleted(function (self $row) {
            $row->premises()->detach();
        });
    }

    protected static function newFactory(): PremisesFeatureFactory
    {
        return PremisesFeatureFactory::new();
    }

    public function featureGroup(): BelongsTo
    {
        return $this->belongsTo(PremisesFeatureGroup::class, 'group_id')->withDefault();
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'icon_id')->withDefault();
    }

    public function premises(): BelongsToMany
    {
        return $this->belongsToMany(
            Premises::class,
            (new PremisesFeatureReference())->getTable(),
            'feature_id',
            'premises_id'
        )->using(PremisesFeatureReference::class)->withTimestamps();
    }

    public function scopePremisesCard(Builder $builder): Builder
    {
        $model = $builder->getModel();

        return $builder->select([
            $model->getTable() . '.id',
            $model->getTable() . '.group_id',
            $model->getTable() . '.icon_id',
            $model->getTable() . '.title'
        ])
            ->whereHas('featureGroup', fn(Builder $query) => $query->select('id')->active()->limit(1))
            ->active()
            ->with([
                'featureGroup' => fn(BelongsTo $belongsTo) => $belongsTo->select(['id', 'priority', 'general', 'title'])
            ])
            ->orderBy($model->getTable() . '.priority')
            ->orderBy($model->getTable() . '.title');
    }

    public function scopeAdminList(Builder $builder): Builder
    {
        $relationGroup = $this->featureGroup();
        $related = $relationGroup->getRelated();
        $relationTable = $related->getTable();
        $tableName = $this->getTable();

        return $builder->leftJoin(
            $relationTable,
            $relationGroup->getQualifiedForeignKeyName(),
            '=',
            $relationGroup->getQualifiedOwnerKeyName()
        )
            ->select([
                $tableName . '.*',
                $relationTable . '.title as groupTitle'
            ])
            ->orderBy($relationTable . '.priority')
            ->orderBy($tableName . '.priority');
    }

    protected function adminTitle(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->groupTitle ?? $this->featureGroup->title) . ': ' . $this->title
        );
    }

    protected function fullTitle(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->relationLoaded('featureGroup') || $this->featureGroup->general) {
                    return $this->title;
                }

                return $this->featureGroup->title . ': ' . $this->title;
            }
        );
    }
}
