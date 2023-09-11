<?php

declare(strict_types=1);

namespace Kelnik\Page\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Site;
use Kelnik\Page\Database\Factories\PageFactory;
use Kelnik\Page\Models\Casts\PageMetaCast;
use Kelnik\Page\Models\Contracts\PageMeta;
use Kelnik\Page\Models\Enums\RedirectType;
use Kelnik\Page\Models\Enums\Type;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $parent_id
 * @property int $site_id
 * @property bool $active
 * @property Type $type
 * @property int $priority
 * @property string $path
 * @property ?string $slug
 * @property RedirectType $redirect_type
 * @property string $title
 * @property string $css_classes
 * @property string $redirect_url
 * @property ?PageMeta $meta
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Page $parent
 * @property Page $parentNested
 * @property Collection $children
 * @property Collection $childrenNested
 * @property Collection $components
 * @property Collection $activeComponents
 *
 * @method Builder active()
 * @method Builder fieldsForFront()
 * @method Builder dynamicComponents()
 */
final class Page extends Model
{
    use AsSource;
    use HasFactory;
    use Filterable;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;

    public ?Site $site = null;

    protected $attributes = [
        'parent_id' => self::DEFAULT_INT_VALUE,
        'site_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'site_id',
        'parent_id',
        'active',
        'priority',
        'path',
        'slug',
        'redirect_type',
        'title',
        'css_classes',
        'redirect_url',
        'meta'
    ];

    protected $casts = [
        'active' => 'boolean',
        'redirect_type' => RedirectType::class,
        'type' => Type::class,
        'meta' => PageMetaCast::class
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->redirect_type = RedirectType::Disabled;
        $this->type = Type::Simple;
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (self $page) {
            if (!$page->type->isSimple()) {
                $page->parent_id = self::DEFAULT_INT_VALUE;
                $page->active = true;
                $page->redirect_type = RedirectType::Disabled;
            }
        });

        self::saving(function (self $page) {
            if (!$page->path || $page->isDirty(['slug'])) {
                $page->path = md5($page->slug ?? '');
            }
        });

        self::updated(function (self $page) {
            if (!$page->isDirty('meta')) {
                return;
            }

            /** @var ?Attachment $originMetaImage */
            $originMetaImage = $page->getOriginal('meta')?->getImage();
            $currentMetaImage = $page->meta?->getImage();

            if ($originMetaImage && $originMetaImage->getKey() !== $currentMetaImage?->getKey()) {
                $page->getOriginal('meta')?->deleteImage();
            }
        });

        self::deleted(function (self $page) {
            $page->meta->deleteImage();
            $page->children()->get()->each->delete();
            $page->components()->get()->each->delete();
        });
    }

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }

    public function isHome(): bool
    {
        return !$this->hasParent() && $this->slug === null;
    }

    public function getUrl(): string
    {
        return '/' . $this->getParentSlugs();
    }

    public function getParentUrl(): string
    {
        return '/' . $this->getParentSlugs(false);
    }

    protected function getParentSlugs(bool $withPageSlug = true): ?string
    {
        if (!$this->hasParent()) {
            return $withPageSlug ? $this->slug : null;
        }

        return ($this->parentNested->slug . ($withPageSlug ? '/' . $this->slug : ''));
    }

    public function hasParent(): bool
    {
        return $this->parent_id > self::DEFAULT_INT_VALUE;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id')->withDefault();
    }

    public function parentNested(): BelongsTo
    {
        return $this->parent()->with('parent');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function childrenNested(): HasMany
    {
        return $this->children()->with('children');
    }

    public function components(): HasMany
    {
        return $this->hasMany(PageComponent::class)->orderBy('priority')->orderBy('id');
    }

    public function activeComponents(): HasMany
    {
        return $this->components()->select(['id', 'page_id', 'component', 'data'])->where('active', true);
    }

    public function hasDynamicComponent(): bool
    {
        if (!$this->relationLoaded('components') || $this->components->isEmpty()) {
            return false;
        }

        return (bool)$this->components->first(static fn(PageComponent $el) => $el->isDynamic());
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }

    public function scopeFieldsForFront(Builder $builder): Builder
    {
        return $builder->select([
            'id',
            'parent_id',
            'site_id',
            'active',
            'slug',
            'redirect_type',
            'title',
            'css_classes',
            'redirect_url'
        ]);
    }

    public function scopeDynamicComponents(Builder $builder, array $params = []): Builder
    {
        return $builder->select('id', 'title')
            ->whereHas('components', static function (Builder $query) use ($params) {
                $components = $params['components'] ?? [];
                $query->withDynamicComponents($components)->limit(1);
            });
    }
}
