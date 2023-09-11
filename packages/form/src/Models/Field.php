<?php

declare(strict_types=1);

namespace Kelnik\Form\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Kelnik\Form\Database\Factories\FieldFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $form_id
 * @property bool $active
 * @property int $priority
 * @property class-string $type
 * @property string $title
 * @property array $params
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Form $form
 *
 * @method Builder active()
 * @method Builder sortByPriority()
 */
final class Field extends Model
{
    use AsSource;
    use Filterable;
    use HasActiveAttribute;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'form_fields';

    protected $attributes = [
        'form_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'active',
        'priority',
        'type',
        'title',
        'params'
    ];

    protected $casts = [
        'active' => 'boolean',
        'params' => 'array'
    ];

    protected array $allowedSorts = [
        'title',
        'created_at',
        'updated_at'
    ];

    protected static function newFactory(): FieldFactory
    {
        return FieldFactory::new();
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id')->withDefault();
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }

    public function scopeSortByPriority(Builder $builder): Builder
    {
        return $builder->orderBy('priority')->orderBy('id');
    }
}
