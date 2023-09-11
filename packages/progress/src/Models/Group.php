<?php

declare(strict_types=1);

namespace Kelnik\Progress\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property ?int $user_id
 * @property bool $active
 * @property string $title
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property User $user
 * @property Collection $images
 * @property Collection $videos
 *
 * @method Builder active()
 */
final class Group extends Model
{
    use AsSource;
    use HasFactory;
    use Filterable;

    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'progress_groups';

    protected $attributes = [
        'active' => false
    ];

    protected $fillable = [
        'active',
        'title'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
