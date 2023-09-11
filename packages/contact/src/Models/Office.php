<?php

declare(strict_types=1);

namespace Kelnik\Contact\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Contact\Database\Factories\OfficeFactory;
use Kelnik\Contact\Models\Casts\CoordsCast;
use Kelnik\Contact\Models\Traits\ScopeActive;
use Kelnik\Core\Map\Contracts\Coords;
use Kelnik\Core\Models\Traits\AttachmentHandler;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property bool $active
 * @property int $priority
 * @property ?int $image_id
 * @property string $title
 * @property string $region
 * @property string city
 * @property string $street
 * @property string $phone
 * @property string $email
 * @property string $route_link
 * @property Coords $coords
 * @property array $schedule
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Attachment $image
 * @property-read string $address
 *
 * @method active(): Builder
 */
final class Office extends Model
{
    use AsSource;
    use AttachmentHandler;
    use HasActiveAttribute;
    use HasFactory;
    use ScopeActive;

    public const PRIORITY_DEFAULT = 500;

    protected $table = 'contacts_offices';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'active',
        'priority',
        'image_id',
        'title',
        'region',
        'city',
        'street',
        'phone',
        'email',
        'route_link',
        'coords',
        'schedule'
    ];

    protected $casts = [
        'active' => 'boolean',
        'coords' => CoordsCast::class,
        'schedule' => 'array'
    ];

    public string $phoneLink = '';
    protected array $attachmentAttributes = ['image_id'];

    protected static function newFactory(): OfficeFactory
    {
        return OfficeFactory::new();
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_id')->withDefault();
    }

    protected function address(): Attribute
    {
        return new Attribute(
            get: fn() => implode(', ', array_filter([$this->region, $this->city, $this->street]))
        );
    }
}
