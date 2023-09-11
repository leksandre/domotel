<?php

declare(strict_types=1);

namespace Kelnik\Document\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Core\Models\Traits\AttachmentHandler;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Kelnik\Document\Database\Factories\ElementFactory;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $category_id
 * @property int $user_id
 * @property bool $active
 * @property int $priority
 * @property int $attachment_id
 * @property string $title
 * @property ?Carbon $publish_date
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Attachment $attachment
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

    protected $table = 'documents_elements';

    protected $attributes = [
        'user_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'active',
        'priority',
        'attachment_id',
        'title',
        'author',
        'publish_date'
    ];

    protected $casts = [
        'active' => 'boolean',
        'publish_date' => 'datetime'
    ];

    protected array $allowedSorts = [
        'title',
        'publish_date',
        'created_at',
        'updated_at'
    ];

    public ?string $publishDateFormatted = null;
    public ?string $attachmentExtension = null;
    public float $attachmentSize = 0;
    public ?string $attachmentSizeFormatted = null;
    public string $attachmentUrl = '#';

    protected array $attachmentAttributes = ['attachment_id'];

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

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'attachment_id')->withDefault();
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }

    public function scopeRelationList($builder)
    {
        return $builder->select(['id', 'category_id', 'title'])
            ->with(['category' => static fn($query) => $query->select('id', 'title')])
            ->latest();
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
