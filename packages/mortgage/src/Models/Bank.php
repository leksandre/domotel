<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Traits\AttachmentHandler;
use Kelnik\Mortgage\Database\Factories\BankFactory;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $priority
 * @property ?int $logo_id
 * @property bool $active
 * @property string $title
 * @property string $link
 * @property string $description
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Attachment $logo
 * @property ?Collection $programs
 */
final class Bank extends Model
{
    use AsSource;
    use AttachmentHandler;
    use Filterable;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;
    public const LOGO_WIDTH = 80;
    public const LOGO_HEIGHT = 80;

    protected $table = 'mortgage_banks';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $fillable = [
        'active', 'priority', 'logo_id', 'title', 'link', 'description'
    ];

    /** @var string[] */
    protected array $attachmentAttributes = ['logo_id'];

    /** @var string|null Logo raster image path with size limits */
    public ?string $logoResizedPath = null;

    /** @var Collection|null Bank programs min/max values */
    public ?Collection $programsParamRange = null;

    protected static function newFactory(): BankFactory
    {
        return BankFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $bank) {
            $bank->programs()->get()->each->delete();
        });
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'logo_id')->withDefault();
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'bank_id')->orderBy('priority');
    }
}
