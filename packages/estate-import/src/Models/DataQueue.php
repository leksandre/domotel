<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateImport\Models\Enums\DataQueueEvent;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $history_id
 * @property bool $done
 * @property DataQueueEvent $event
 * @property string $model
 * @property array $data
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property History $history
 */
final class DataQueue extends EstateModel
{
    use AsSource;
    use HasFactory;

    protected $table = 'estate_import_data_queue';

    protected $casts = [
        'data' => 'array',
        'event' => DataQueueEvent::class
    ];

    protected $attributes = [
        'data' => '[]'
    ];

    protected $fillable = [
        'done', 'event', 'model', 'data'
    ];

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['event'])) {
            $attributes['event'] = DataQueueEvent::UnProcessed->value;
        }

        parent::__construct($attributes);
    }

    public function history(): BelongsTo
    {
        return $this->belongsTo(History::class, 'id', 'history_id')->withDefault();
    }

    public function setEventAdded(): void
    {
        $this->event = DataQueueEvent::Added;
    }

    public function setEventUpdated(): void
    {
        $this->event = DataQueueEvent::Updated;
    }

    public function setEventDeleted(): void
    {
        $this->event = DataQueueEvent::Deleted;
    }

    public function setEventDeclined(): void
    {
        $this->event = DataQueueEvent::Declined;
    }
}
