<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Mortgage\Database\Factories\ProgramFactory;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $bank_id
 * @property int $priority
 * @property bool $active
 * @property int $min_time
 * @property int $max_time
 * @property float $min_payment_percent
 * @property float $max_payment_percent
 * @property float $rate
 * @property string $title
 * @property string $comment
 * @property string $description
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Bank $bank
 */
final class Program extends Model
{
    use AsSource;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;
    public const MIN_INT_VALUE = 0;
    public const MIN_FLOAT_VALUE = 0;

    protected $table = 'mortgage_programs';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false,
        'min_time' => self::MIN_INT_VALUE,
        'max_time' => self::MIN_INT_VALUE,
        'min_payment_percent' => self::MIN_FLOAT_VALUE,
        'max_payment_percent' => self::MIN_FLOAT_VALUE,
        'rate' => self::MIN_FLOAT_VALUE
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $fillable = [
        'active', 'priority', 'min_time', 'max_time', 'min_payment_percent', 'max_payment_percent',
        'rate', 'title', 'comment', 'description'
    ];

    protected static function newFactory(): ProgramFactory
    {
        return ProgramFactory::new();
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id')->withDefault();
    }
}
