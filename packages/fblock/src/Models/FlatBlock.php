<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\FBlock\Database\Factories\BlockFactory;
use Kelnik\FBlock\Models\Casts\ButtonCast;
use Kelnik\Form\View\Components\Form\FormDto;
use Orchid\Attachment\Models\Attachment;
use Orchid\Attachment\Models\Attachmentable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property bool $active
 * @property int $priority
 * @property string $title
 * @property string $area
 * @property string $floor
 * @property string $price
 * @property string $planoplan_code
 * @property Collection $features
 * @property \Kelnik\FBlock\Models\Contracts\Button $button
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection $images
 *
 * @method Builder active()
 */
final class FlatBlock extends Model
{
    use AsSource;
    use HasFactory;
    use Filterable;

    public const PRIORITY_DEFAULT = 500;

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'active',
        'priority',
        'title',
        'area',
        'floor',
        'price',
        'planoplan_code',
        'features'
    ];

    protected $casts = [
        'active' => 'boolean',
        'features' => 'array',
        'button' => ButtonCast::class
    ];

    public ?Collection $imageSlider = null;
    public ?FormDto $callbackForm = null;

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $page) {
            $page->images()->get()->each->delete();
        });
    }

    protected static function newFactory(): BlockFactory
    {
        return BlockFactory::new();
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(
            Attachment::class,
            'attachmentable',
            (new Attachmentable())->getTable(),
            'attachmentable_id',
            'attachment_id'
        )->orderBy('sort');
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
