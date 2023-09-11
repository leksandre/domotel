<?php

declare(strict_types=1);

namespace Kelnik\Menu\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Kelnik\Menu\Database\Factories\MenuFactory;
use Kelnik\Menu\Models\Enums\Type;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property bool $active
 * @property Type $type
 * @property string $title
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection $items
 * @property Collection $itemsTree
 *
 * @method Builder active()
 */
final class Menu extends Model
{
    use AsSource;
    use Filterable;
    use HasActiveAttribute;
    use HasFactory;

    protected $table = 'menu';
    public string $menuTemplate = '';

    protected $attributes = [
        'active' => false
    ];

    protected $fillable = [
        'active', 'type', 'title'
    ];

    protected $casts = [
        'active' => 'boolean',
        'type' => Type::class
    ];

    protected array $allowedSorts = [
        'title', 'created_at', 'updated_at'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $menu) {
            $menu->items()->get()->each->delete();
        });
    }

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['type'])) {
            $attributes['type'] = Type::Tree->value;
        }

        parent::__construct($attributes);
    }

    protected static function newFactory(): MenuFactory
    {
        return MenuFactory::new();
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id')->sortByPriority();
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
