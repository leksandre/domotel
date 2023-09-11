<?php

declare(strict_types=1);

namespace Kelnik\Form\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Traits\HasActiveAttribute;
use Kelnik\Form\Database\Factories\FormFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $policy_page_id
 * @property bool $active
 * @property string $slug
 * @property string $title
 * @property string $success_title
 * @property string $error_title
 * @property string $notify_title
 * @property string $button_text
 * @property string $description
 * @property string $success_text
 * @property string $error_text
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection $fields
 * @property Collection $emails
 * @property Collection $logs
 *
 * @method active(): Builder
 */
final class Form extends Model
{
    use AsSource;
    use Filterable;
    use HasActiveAttribute;
    use HasFactory;

    protected $table = 'forms';

    protected $attributes = [
        'active' => false
    ];

    protected $fillable = [
        'policy_page_id',
        'active',
        'title',
        'success_title',
        'error_title',
        'notify_title',
        'slug',
        'button_text',
        'description',
        'success_text',
        'error_text'
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

        self::deleted(function (self $form) {
            $form->fields()->get()->each->delete();
            $form->logs()->get()->each->delete();
            $form->emails()->get()->each->delete();
        });
    }

    protected static function newFactory(): FormFactory
    {
        return FormFactory::new();
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class, 'form_id')->sortByPriority();
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'form_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'form_id')->latest();
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
