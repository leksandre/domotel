<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Database\Factories\PremisesTypeGroupFactory;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Traits\ScopeAdminList;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $priority
 * @property int $image_id
 * @property bool $living
 * @property bool $build_title
 * @property string $title
 * @property string $slug
 * @property string $external_id
 * @property array $plural
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection $types
 * @property Attachment $image
 *
 * @method Builder adminList()
 */
final class PremisesTypeGroup extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use ScopeAdminList;

    protected $table = 'estate_premises_type_groups';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'living' => false,
        'build_title' => false
    ];

    protected $casts = [
        'living' => 'boolean',
        'build_title' => 'boolean',
        'plural' => 'array'
    ];

    protected $fillable = [
        'priority',
        'image_id',
        'title',
        'living',
        'build_title',
        'slug',
        'external_id',
        'plural'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'title',
        'living',
        'slug',
        'external_id',
        'created_at',
        'updated_at'
    ];

    protected array $attachmentAttributes = [
        'image_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $row) {
            $row->types()->delete();
        });
    }

    protected static function newFactory(): PremisesTypeGroupFactory
    {
        return PremisesTypeGroupFactory::new();
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_id')->withDefault();
    }

    public function types(): HasMany
    {
        return $this->hasMany(PremisesType::class, 'group_id')->orderBy('priority')->orderBy('title');
    }
}
