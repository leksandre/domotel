<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $complex_id
 * @property int $priority
 * @property string $title
 * @property string $slug
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Complex $complex
 * @property Collection $premises
 * @property Attachment $listImage
 * @property Attachment $cardImage
 *
 * @method Builder adminList()
 */
final class PremisesPlanType extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $table = 'estate_premises_plan_types';

    protected $fillable = [
        'complex_id',
        'priority',
        'list_image_id',
        'card_image_id',
        'title',
        'slug',
        'external_id'
    ];

    protected $attributes = [
        'complex_id' => 0,
        'priority' => self::PRIORITY_DEFAULT
    ];

    protected array $allowedFilters = [
        'id' => Where::class,
        'title' => Like::class,
        'external_id' => Like::class
    ];

    protected array $allowedSorts = [
        'title',
        'priority',
        'external_id',
        'created_at',
        'updated_at'
    ];

    protected array $attachmentAttributes = [
        'list_image_id',
        'card_image_id',
    ];

    public function complex(): BelongsTo
    {
        return $this->belongsTo(Complex::class, 'complex_id')->withDefault();
    }

    public function premises(): HasMany
    {
        return $this->hasMany(Premises::class, 'plan_type_id');
    }

    public function listImage(): HasOne
    {
        return $this->hasOne(Attachment::class, 'list_image_id')->withDefault();
    }

    public function cardImage(): HasOne
    {
        return $this->hasOne(Attachment::class, 'card_image_id')->withDefault();
    }

    public function scopeAdminList(Builder $query): Builder
    {
        return $query->with(['complex' => function (BelongsTo $query) {
            $query->adminList();
        }])
            ->select(['id', 'complex_id', 'title'])
            ->orderBy('complex_id')
            ->orderBy('priority')
            ->orderBy('title');
    }

    protected function adminTitle(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->complex->title . ' > ' . $this->title
        );
    }
}
