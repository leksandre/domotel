<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Database\Factories\PremisesTypeFactory;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Contracts\HasReplacement;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $group_id
 * @property int $replace_id
 * @property int $priority
 * @property int $rooms
 * @property string $title
 * @property string $short_title
 * @property string $slug
 * @property string $color
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property PremisesTypeGroup $typeGroup
 * @property PremisesType $replaceType
 *
 * @property-read string $admin_title
 * @method Builder adminList()
 * @method Builder premisesCard()
 */
final class PremisesType extends EstateModel implements HasReplacement
{
    use AsSource;
    use Filterable;
    use HasFactory;

    public const ROOMS_DEFAULT = 0;
    public const COLOR_DEFAULT = '#000';

    protected $table = 'estate_premises_types';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'replace_id' => 0,
        'rooms' => self::ROOMS_DEFAULT,
        'color' => self::COLOR_DEFAULT
    ];

    protected $fillable = [
        'replace_id',
        'priority',
        'title',
        'short_title',
        'slug',
        'rooms',
        'color',
        'external_id'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'title',
        'slug',
        'rooms',
        'color',
        'external_id',
        'created_at',
        'updated_at'
    ];

    protected static function newFactory(): PremisesTypeFactory
    {
        return PremisesTypeFactory::new();
    }

    public function typeGroup(): BelongsTo
    {
        return $this->belongsTo(PremisesTypeGroup::class, 'group_id', 'id')->withDefault();
    }

    public function replaceType(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replace_id', 'id')->withDefault();
    }

    public function getReplacementField(): string
    {
        return 'replace_id';
    }

    public function scopeAdminList(Builder $builder): Builder
    {
        $relationGroup = $this->typeGroup();
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
                $relationTable . '.priority as groupPriority',
                $relationTable . '.title as groupTitle'
            ])
            ->orderBy($relationTable . '.priority')
            ->orderBy($tableName . '.priority');
    }

    public function scopePremisesCard(Builder $builder): Builder
    {
        return $builder->select(['id', 'group_id', 'rooms', 'slug', 'title', 'short_title'])
            ->with([
                'typeGroup' => fn(BelongsTo $b) => $b
                    ->select('id', 'image_id', 'living', 'build_title', 'slug', 'title')
                    ->with('image')
            ]);
    }

    protected function adminTitle(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->groupTitle ?? $this->typeGroup->title) . ': ' . $this->title
        );
    }
}
