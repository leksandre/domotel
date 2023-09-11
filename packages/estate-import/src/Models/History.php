<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateImport\Models\Enums\HistoryState;
use Kelnik\EstateImport\Repositories\Contracts\DataQueueRepository;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property HistoryState $state
 * @property string $hash
 * @property string $batch_id
 * @property string $pre_processor
 * @property array $pre_processor_data
 * @property array $result
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection $dataQueue
 */
final class History extends EstateModel
{
    use AsSource;
    use HasFactory;

    protected $table = 'estate_import_history';

    protected $casts = [
        'pre_processor_data' => 'array',
        'result' => 'array',
        'state' => HistoryState::class
    ];

    protected $attributes = [
        'pre_processor_data' => '[]',
        'result' => '[]'
    ];

    protected $fillable = [
        'state', 'hash', 'batch_id', 'pre_processor', 'pre_processor_data', 'result'
    ];

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['state'])) {
            $attributes['state'] = HistoryState::New->value;
        }

        parent::__construct($attributes);
    }

    protected static function boot(): void
    {
        parent::boot();

        self::updated(function (self $history) {
            if ($history->state->isError() && $history->getOriginal('state')->isPreProcessing()) {
                resolve(DataQueueRepository::class)->forceDeleteByHistory($history);
            }
        });

        self::deleted(function (self $history) {
            resolve(DataQueueRepository::class)->forceDeleteByHistory($history);
        });
    }

    public function dataQueue(): HasMany
    {
        return $this->hasMany(DataQueue::class, 'history_id');
    }

    public function setStateIsReady(): void
    {
        $this->state = HistoryState::Ready;
    }

    public function setStateIsPreProcess(): void
    {
        $this->state = HistoryState::PreProcess;
    }

    public function setStateIsProcess(): void
    {
        $this->state = HistoryState::Process;
    }

    public function setStateIsDone(): void
    {
        $this->state = HistoryState::Done;
    }

    public function setStateIsError(): void
    {
        $this->state = HistoryState::Error;
    }

    public function setResultForState(array $newResult): void
    {
        $this->result = array_merge_recursive(
            $this->result,
            [$this->state->value => $newResult]
        );
    }

    public function getError(): ?array
    {
        return $this->result[HistoryState::Error->value] ?? null;
    }
}
