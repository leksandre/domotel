<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Database\Factories\PremisesStatusFactory;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Contracts\HasReplacement;
use Kelnik\Estate\Models\Traits\ScopeAdminList;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $replace_id
 * @property int $priority
 * @property int $icon_id
 * @property bool $premises_card_available
 * @property bool $hide_price
 * @property bool $take_stat
 * @property string $title
 * @property string $additional_text
 * @property string $color
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Attachment $icon
 * @property-read bool $card_available
 * @property-read bool $price_is_visible
 *
 * @method Builder adminList()
 * @method Builder premisesCard()
 */
final class PremisesStatus extends EstateModel implements HasReplacement
{
    use AsSource;
    use Filterable;
    use HasFactory;
    use ScopeAdminList;

    public const COLOR_DEFAULT = '#000';

    /**
     * @var int
     * @deprecated
     */
    public const BOOKED = 2;

    protected $table = 'estate_premises_statuses';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'replace_id' => 0,
        'color' => self::COLOR_DEFAULT
    ];

    protected $fillable = [
        'priority',
        'replace_id',
        'icon_id',
        'premises_card_available',
        'hide_price',
        'take_stat',
        'title',
        'additional_text',
        'color',
        'external_id'
    ];

    protected $casts = [
        'premises_card_available' => 'boolean',
        'hide_price' => 'boolean',
        'take_stat' => 'boolean'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'title',
        'color',
        'external_id',
        'created_at',
        'updated_at'
    ];

    protected array $attachmentAttributes = [
        'icon_id'
    ];

    protected static function newFactory(): PremisesStatusFactory
    {
        return PremisesStatusFactory::new();
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'icon_id')->withDefault();
    }

    public function getReplacementField(): string
    {
        return 'replace_id';
    }

    public function scopePremisesCard(Builder $builder): Builder
    {
        return $builder->select([
            'id', 'icon_id', 'premises_card_available', 'hide_price', 'title', 'additional_text', 'color'
        ])->with(['icon']);
    }

    protected function cardAvailable(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->premises_card_available
        );
    }

    protected function priceIsVisible(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->hide_price
        );
    }
}
