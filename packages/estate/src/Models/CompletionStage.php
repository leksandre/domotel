<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $completion_id
 * @property int $priority
 * @property string $title
 * @property string $external_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Completion $completion
 */
final class CompletionStage extends EstateModel
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $table = 'estate_completion_stages';

    protected $attributes = [
        'priority' => self::PRIORITY_DEFAULT
    ];

    protected $fillable = [
        'priority',
        'title',
        'external_id'
    ];

    protected array $allowedSorts = [
        'id',
        'priority',
        'title',
        'created_at',
        'updated_at'
    ];

    public function completion(): BelongsTo
    {
        return $this->belongsTo(Completion::class)->withDefault();
    }
}