<?php

declare(strict_types=1);

namespace Kelnik\News\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Traits\AttachmentHandler;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Kelnik\News\Database\Factories\ElementFactory;
use Kelnik\News\Models\Casts\ElementButtonCast;
use Kelnik\News\Models\Casts\ElementMetaCast;
use Orchid\Attachment\Models\Attachment;
use Orchid\Attachment\Models\Attachmentable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property ?int $category_id
 * @property ?int $user_id
 * @property bool $active
 * @property bool $show_timer
 * @property int $priority
 * @property ?int $preview_image
 * @property ?int $body_image
 * @property string $slug
 * @property string $title
 * @property string $preview
 * @property string $body
 * @property \Kelnik\News\Models\Contracts\ElementButton $button
 * @property ?\Kelnik\News\Models\Contracts\ElementMeta $meta
 * @property ?Carbon $active_date_start
 * @property ?Carbon $active_date_finish
 * @property ?Carbon $publish_date
 * @property ?Carbon $publish_date_start
 * @property ?Carbon $publish_date_finish
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Attachment $previewImage
 * @property Attachment $bodyImage
 * @property Collection $images
 * @property Category $category
 *
 * @method Builder active()
 * @method Builder relationList()
 */
final class Element extends Model
{
    use AsSource;
    use AttachmentHandler;
    use Filterable;
    use HasActiveAttribute;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'news_elements';

    protected $attributes = [
        'user_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false,
        'show_timer' => false
    ];

    protected $fillable = [
        'active',
        'show_timer',
        'priority',
        'preview_image',
        'body_image',
        'slug',
        'title',
        'preview',
        'body',
        'active_date_start',
        'active_date_finish',
        'publish_date',
        'publish_date_start',
        'publish_date_finish',
        'meta'
    ];

    protected $casts = [
        'active' => 'boolean',
        'show_timer' => 'boolean',
        'active_date_start' => 'datetime',
        'active_date_finish' => 'datetime',
        'publish_date' => 'datetime',
        'publish_date_start' => 'datetime',
        'publish_date_finish' => 'datetime',
        'button' => ElementButtonCast::class,
        'meta' => ElementMetaCast::class
    ];

    protected array $allowedSorts = [
        'title',
        'slug',
        'active_date_start',
        'active_date_finish',
        'publish_date',
        'publish_date_start',
        'publish_date_finish',
        'created_at',
        'updated_at'
    ];

    /** @var string[] */
    protected array $attachmentAttributes = ['preview_image', 'body_image'];

    public ?string $publishDateFormatted = null;
    public ?string $publishDateStartFormatted = null;
    public ?string $publishDateFinishFormatted = null;

    public string $url = '#';
    public ?string $routeName = null;

    public ?string $previewImagePicture = null;
    public ?string $bodyImagePicture = null;
    public ?Collection $imageSlider = null;

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $element) {
            $element->images()->delete();
        });
    }

    protected static function newFactory(): ElementFactory
    {
        return ElementFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault();
    }

    public function previewImage(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'preview_image')->withDefault();
    }

    public function bodyImage(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'body_image')->withDefault();
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

    public function scopeRelationList($builder)
    {
        return $builder->select(['id', 'category_id', 'title'])
            ->with(['category' => static fn($query) => $query->select('id', 'title')])
            ->orderByDesc('publish_date')
            ->orderByDesc('id');
    }

    protected function relation(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->category->title
                ? $this->title
                : $this->category->title . ': ' . $this->title
        );
    }
}
