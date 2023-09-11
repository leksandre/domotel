<?php

declare(strict_types=1);

namespace Kelnik\Progress\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Core\Models\Traits\AttachmentHandler;
use Kelnik\Progress\Database\Factories\CameraFactory;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property ?int $user_id
 * @property ?int $group_id
 * @property bool $active
 * @property int $priority
 * @property ?int $cover_image
 * @property string $url
 * @property string $title
 * @property string $description
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Group $group
 * @property User $user
 * @property Attachment $cover
 *
 * @method Builder active()
 */
final class Camera extends Model
{
    use AsSource;
    use AttachmentHandler;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'progress_cameras';

    protected $attributes = [
        'user_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'cover_image',
        'active',
        'title',
        'url',
        'description',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    /** @var string[] */
    protected array $attachmentAttributes = ['cover_image'];

    protected static function newFactory(): CameraFactory
    {
        return CameraFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'cover_image')->withDefault();
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
