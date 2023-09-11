<?php

declare(strict_types=1);

namespace Kelnik\Menu\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Traits\AttachmentHandler;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Menu\Database\Factories\MenuItemFactory;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageService;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property ?int $menu_id
 * @property ?int $parent_id
 * @property ?int $page_id
 * @property int $page_component_id
 * @property bool $active
 * @property bool $marked
 * @property int $priority
 * @property ?int $icon_image
 * @property string $title
 * @property string $link
 * @property array $params
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Menu $menu
 * @property MenuItem $parent
 * @property MenuItem $parentNested
 * @property Collection $children
 * @property Collection $childrenNested
 * @property Page $page
 * @property PageComponent $pageComponent
 * @property Attachment $icon
 *
 * @property-read string $url
 * @property bool $selected
 *
 * @method Builder active()
 * @method Builder tree()
 * @method Builder sortByPriority()
 *
 * @TODO: Remove Page and PageComponent database relations
 */
final class MenuItem extends Model
{
    use AsSource;
    use AttachmentHandler;
    use Filterable;
    use HasActiveAttribute;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'menu_items';

    protected $attributes = [
        'menu_id' => self::DEFAULT_INT_VALUE,
        'parent_id' => self::DEFAULT_INT_VALUE,
        'page_id' => self::DEFAULT_INT_VALUE,
        'page_component_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT,
        'icon_image' => self::DEFAULT_INT_VALUE,
        'active' => false,
        'marked' => false
    ];

    protected $fillable = [
        'active',
        'marked',
        'priority',
        'icon_image',
        'title',
        'link',
        'params'
    ];

    protected $casts = [
        'active' => 'boolean',
        'marked' => 'boolean',
        'params' => 'array'
    ];

    protected array $allowedSorts = [
        'title',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'url',
        'selected'
    ];

    /** @var string[] */
    protected array $attachmentAttributes = ['icon_image'];

    private ?string $url = null;
    private bool $selected = false;

    public ?string $iconBody = null;

    protected static function newFactory(): MenuItemFactory
    {
        return MenuItemFactory::new();
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id')->withDefault();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id')->withDefault();
    }

    public function parentNested(): BelongsTo
    {
        return $this->parent()->with('parent');
    }

    public function hasParent(): bool
    {
        return $this->parent_id !== null && $this->parent_id > self::DEFAULT_INT_VALUE;
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->sortByPriority();
    }

    public function childrenNested(): HasMany
    {
        return $this->children()->with('childrenNested');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id')->withDefault();
    }

    public function pageComponent(): BelongsTo
    {
        return $this->belongsTo(PageComponent::class, 'page_component_id')->withDefault();
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'icon_image')->withDefault();
    }

    public function getUrl(bool $absolute = true): string
    {
        if (!$this->relationLoaded('page') || !$this->page?->exists) {
            return $this->link ?? '';
        }

        /** @var PageService $pageService */
        $pageService = resolve(PageService::class);
        $usePageComponent = $this->relationLoaded('pageComponent') && $this->pageComponent->hasContentAlias();

        return $usePageComponent
            ? $pageService->getPageComponentUrl($this->page, $this->pageComponent, [], $absolute)
            : $pageService->getPageUrl($this->page, [], $absolute);
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->url === null) {
                    $site = resolve(SiteService::class)->findPrimary();
                    $absolute = $this->relationLoaded('page') && $this->page->site_id !== $site?->getKey();

                    $this->url = $this->getUrl($absolute);
                }

                return $this->url;
            }
        );
    }

    protected function selected(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->selected,
            set: fn($value) => $this->selected = (bool)$value
        );
    }

    protected function pageId(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $this->resetUrl();

                return $value;
            }
        );
    }

    protected function pageComponentId(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $this->resetUrl();

                return $value;
            }
        );
    }

    protected function link(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $this->resetUrl();

                return $value;
            }
        );
    }

    private function resetUrl(): void
    {
        $this->url = null;
        $this->selected = false;
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }

    public function scopeTree(Builder $builder): Builder
    {
        return $builder->where('parent_id', '=', self::DEFAULT_INT_VALUE)->with('childrenNested');
    }

    public function scopeSortByPriority(Builder $builder): Builder
    {
        return $builder->orderBy('priority')->orderBy('id');
    }
}
