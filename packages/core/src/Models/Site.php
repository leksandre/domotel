<?php

declare(strict_types=1);

namespace Kelnik\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Core\Database\Factories\SiteFactory;
use Kelnik\Core\Models\Casts\SiteSettingsCast;
use Kelnik\Core\Models\Enums\Lang;
use Kelnik\Core\Models\Enums\Type;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property bool $active
 * @property bool $primary
 * @property Type $type
 * @property Lang $locale
 * @property string $title
 * @property \Kelnik\Core\Models\Contracts\SiteSettings $settings
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection $hosts
 */
final class Site extends Model
{
    use AsSource;
    use HasFactory;

    protected $table = 'sites';

    protected $fillable = [
        'title',
        'active',
        'primary',
        'type',
        'locale',
        'settings'
    ];

    protected $casts = [
        'settings' => SiteSettingsCast::class,
        'type' => Type::class,
        'locale' => Lang::class
    ];

    protected $attributes = [
        'active' => false,
        'primary' => false
    ];

    public function __construct(array $attributes = [])
    {
        $defValues = [
            'type' => Type::Site->value,
            'locale' => Lang::Russian->value
        ];

        foreach ($defValues as $fieldName => $value) {
            if (!isset($attributes[$fieldName])) {
                $attributes[$fieldName] = is_callable($value) ? $value() : $value;
            }
        }

        parent::__construct($attributes);
    }

    protected static function boot(): void
    {
        parent::boot();

        self::saved(function (self $site) {
            if ($site->isDirty('primary') && $site->primary) {
                self::withoutEvents(fn() => self::query()->whereKeyNot($site->getKey())->update(['primary' => false]));
            }
        });

        self::deleted(function (self $site) {
            $site->hosts()->delete(); // without events
        });
    }

    protected static function newFactory(): SiteFactory
    {
        return SiteFactory::new();
    }

    public function hosts(): HasMany
    {
        return $this->hasMany(Host::class, 'site_id', 'id')->orderBy('value');
    }
}
