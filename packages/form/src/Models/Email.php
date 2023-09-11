<?php

declare(strict_types=1);

namespace Kelnik\Form\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Form\Database\Factories\EmailFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $form_id
 * @property string $email
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Form $form
 */
final class Email extends Model
{
    use AsSource;
    use Filterable;
    use HasFactory;

    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'form_emails';

    protected $attributes = [
        'form_id' => self::DEFAULT_INT_VALUE
    ];

    protected $fillable = [
        'email'
    ];

    protected static function newFactory(): EmailFactory
    {
        return EmailFactory::new();
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id')->withDefault();
    }
}
