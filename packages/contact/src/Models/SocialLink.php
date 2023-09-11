<?php

declare(strict_types=1);

namespace Kelnik\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kelnik\Contact\Database\Factories\SocialLinkFactory;
use Kelnik\Contact\Models\Traits\ScopeActive;
use Kelnik\Core\Models\Traits\AttachmentHandler;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property bool $active
 * @property int $priority
 * @property ?int $icon_id
 * @property string $title
 * @property string $link
 *
 * @property Attachment $icon
 *
 * @method active(): Builder
 */
final class SocialLink extends Model
{
    use AsSource;
    use AttachmentHandler;
    use HasActiveAttribute;
    use HasFactory;
    use ScopeActive;

    public const PRIORITY_DEFAULT = 500;

    protected $table = 'contacts_social_links';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'active',
        'priority',
        'title',
        'icon_id',
        'link'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];
    protected array $attachmentAttributes = ['icon_id'];

    public ?string $iconPath = null;
    public ?string $iconBody = null;

    protected static function newFactory(): SocialLinkFactory
    {
        return SocialLinkFactory::new();
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'icon_id')->withDefault();
    }
}
