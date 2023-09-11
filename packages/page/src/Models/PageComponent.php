<?php

declare(strict_types=1);

namespace Kelnik\Page\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Page\Database\Factories\PageComponentFactory;
use Kelnik\Page\Models\Casts\ComponentDataProviderCast;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageDynamicComponent;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $page_id
 * @property bool $active
 * @property int $priority
 * @property class-string $component
 * @property ComponentDataProvider $data
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Page $page
 * @property Collection $routes
 *
 * @method Builder active()
 * @method Builder fieldsForFront()
 * @method Builder withDynamicComponents()
 */
final class PageComponent extends Model
{
    use AsSource;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'page_components';

    protected $attributes = [
        'page_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'active',
        'priority',
        'component',
        'data'
    ];

    protected $casts = [
        'page_id' => 'integer',
        'active' => 'boolean',
        'priority' => 'integer',
        'data' => ComponentDataProviderCast::class
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(static function (self $pageComponent) {
            $pageComponent->routes()->get()->each->delete();
            $pageComponent->data->delete();
        });
    }

    protected static function newFactory(): PageComponentFactory
    {
        return PageComponentFactory::new();
    }

    public function isDynamic(): bool
    {
        return is_a($this->component, KelnikPageDynamicComponent::class, true);
    }

    public function hasContentAlias(): bool
    {
        return is_a($this->component, HasContentAlias::class, true);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id')->withDefault();
    }

    public function routes(): HasMany
    {
        return $this->hasMany(PageComponentRoute::class, 'page_component_id');
    }

    public function available(): bool
    {
        return $this->componentAvailable();
    }

    public function editable(): bool
    {
        return $this->componentAvailable();
    }

    public function removable(): bool
    {
        return true;
    }

    private function componentAvailable(): bool
    {
        return class_exists($this->component);
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }

    public function scopeFieldsForFront(Builder $builder): Builder
    {
        return $builder->select(['id', 'page_id', 'active', 'component', 'data']);
    }

    public function scopeWithDynamicComponents(Builder $builder, array $components = []): Builder
    {
        $builder->whereHas('routes', static fn(Builder $query) => $query->select(['id'])->limit(1));

        if ($components) {
            $builder->whereIn('component', $components);
        }

        return $builder;
    }
}
