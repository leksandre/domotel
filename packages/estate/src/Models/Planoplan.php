<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Database\Factories\PlanoplanFactory;
use Kelnik\Estate\Models\Planoplan\Contracts\Widget;
use Kelnik\Estate\Models\Planoplan\WidgetFactory;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;
use Orchid\Screen\AsSource;

/**
 * @property string $id
 * @property bool $active
 * @property int $version
 * @property array $data
 * @property Collection $premises
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property-read ?Widget $widget
 *
 * @method Builder active()
 */
final class Planoplan extends Model
{
    use AsSource;
    use HasFactory;
    use HasUlids;

    public const VERSION_3 = 3;
    public const VERSION_4 = 4;
    public const DEFAULT_VERSION = 0;
    public const VERSIONS = [self::VERSION_3, self::VERSION_4];
    public const CODE_MAX_LENGTH = 100;

    protected $table = 'estate_planoplan';

    protected $fillable = [
        'id',
        'version',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    protected $attributes = [
        'active' => false,
        'version' => self::DEFAULT_VERSION,
        'data' => '[]'
    ];

    public function delete(): ?bool
    {
        return !$this->exists || resolve(PlanoplanRepository::class)->hasPremises($this)
            ? false
            : parent::delete();
    }

    protected static function newFactory(): PlanoplanFactory
    {
        return PlanoplanFactory::new();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function premises(): HasMany
    {
        return $this->hasMany(Premises::class, 'planoplan_code');
    }

    public function isAvailable(): bool
    {
        return $this->exists && $this->active && $this->data;
    }

    protected function widget(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->isAvailable() ? WidgetFactory::make($this) : null
        );
    }
}
