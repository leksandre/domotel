<?php

declare(strict_types=1);

namespace Kelnik\Progress\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Progress\Database\Factories\AlbumFactory;
use Orchid\Attachment\Models\Attachment;
use Orchid\Attachment\Models\Attachmentable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $user_id
 * @property int $group_id
 * @property bool $active
 * @property string $title
 * @property string $comment
 * @property string $description
 * @property ?Carbon $publish_date
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property User $user
 * @property Group $group
 * @property Collection<Attachmentable> $images
 * @property Collection<AlbumVideo> $videos
 *
 * @method Builder active()
 */
final class Album extends Model
{
    use AsSource;
    use HasFactory;
    use Filterable;

    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'progress_albums';

    protected $attributes = [
        'user_id' => self::DEFAULT_INT_VALUE,
        'active' => false
    ];

    protected $fillable = [
        'active',
        'title',
        'comment',
        'description',
        'publish_date'
    ];

    protected $casts = [
        'active' => 'boolean',
        'publish_date' => 'date'
    ];

    /** @var ?string cover image link */
    public ?string $coverImage = null;

    /** @var ?string picture tag content */
    public ?string $coverPicture = null;

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $album) {
            $album->images()->delete();
            $album->videos()->delete();
        });
    }

    protected static function newFactory(): AlbumFactory
    {
        return AlbumFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
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

    public function videos(): HasMany
    {
        return $this->hasMany(AlbumVideo::class, 'album_id')->orderBy('priority');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id')->withDefault();
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
