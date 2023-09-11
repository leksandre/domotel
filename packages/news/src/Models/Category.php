<?php

declare(strict_types=1);

namespace Kelnik\News\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Kelnik\News\Database\Factories\CategoryFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $user_id
 * @property bool $active
 * @property int $priority
 * @property string $slug
 * @property string $title
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property User $user
 * @property Collection $elements
 *
 * @method Builder active()
 */
final class Category extends Model
{
    use AsSource;
    use Filterable;
    use HasActiveAttribute;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'news_categories';

    protected $attributes = [
        'user_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT,
        'active' => false
    ];

    protected $fillable = [
        'active', 'priority', 'slug', 'title'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected array $allowedSorts = [
        'title', 'slug', 'created_at', 'updated_at'
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $category) {
            $category->elements()->get()->each->delete();
        });
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function elements(): HasMany
    {
        return $this->hasMany(Element::class, 'category_id');
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
