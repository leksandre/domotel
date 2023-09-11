<?php

declare(strict_types=1);

namespace Kelnik\Page\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $page_component_id
 * @property bool $ignore_page_slug
 * @property string $path
 * @property array $middlewares
 * @property ?Collection $params
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property PageComponent $pageComponent
 * @property Page $page
 * @property Collection $elements
 */
final class PageComponentRoute extends Model
{
    use AsSource;
    use HasFactory;

    public const DEFAULT_INT_VALUE = 0;

    protected $attributes = [
        'ignore_page_slug' => false,
        'page_component_id' => self::DEFAULT_INT_VALUE,
        'path' => '',
        'middlewares' => '[]'
    ];

    protected $casts = [
        'page_component_id' => 'integer',
        'ignore_page_slug' => 'boolean',
        'middlewares' => 'array',
        'params' => 'collection'
    ];

    protected $fillable = [
        'ignore_page_slug', 'path', 'middlewares', 'params'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(static function (self $componentRoute) {
            $componentRoute->elements()->get()->each->delete();
        });
    }

    public function pageComponent(): BelongsTo
    {
        return $this->belongsTo(PageComponent::class, 'page_component_id')->withDefault();
    }

    public function page(): HasOneThrough
    {
        return $this->hasOneThrough(
            Page::class,
            PageComponent::class,
            'id',
            'id',
            'page_component_id',
            'page_id'
        )->withDefault();
    }

    public function elements(): HasMany
    {
        return $this->hasMany(PageComponentRouteElement::class, 'page_component_route_id');
    }
}
