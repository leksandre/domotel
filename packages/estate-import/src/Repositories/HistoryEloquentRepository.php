<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories;

use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\LazyCollection;
use Kelnik\EstateImport\Models\Enums\HistoryState;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;

final class HistoryEloquentRepository implements HistoryRepository
{
    protected string $modelNamespace = History::class;

    public function findByPrimary(int|string $primary): History
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getAdminList(): LengthAwarePaginator
    {
        return $this->modelNamespace::orderByDesc('id')->paginate();
    }

    public function save(History $model): bool
    {
        return $model->save();
    }

    public function getQueueRow(): History
    {
        return $this->modelNamespace::query()
            ->where('state', '=', HistoryState::Ready->value)
            ->orWhere(
                static fn(Builder $builder) => $builder
                    ->where('state', '=', HistoryState::New->value)
                    ->whereNotNull('pre_processor')
            )
            ->orderByRaw('FIELD(`state`, ?, ?)', [HistoryState::Ready->value, HistoryState::New->value])
            ->firstOrNew();
    }

    public function getCompletedRowsFromDate(DateTimeInterface $dateFrom): LazyCollection
    {
        return $this->modelNamespace::where('state', HistoryState::Done->value)
            ->whereDate('created_at', '<=', $dateFrom)
            ->oldest()
            ->cursor();
    }

    public function hasSameHash(History $history, ?DateTimeInterface $dateFrom = null): bool
    {
        $query = $this->modelNamespace::select(['id', 'hash'])
            ->where('id', '<', $history->getKey())
            ->orderByDesc('id');

        if ($dateFrom === null) {
            return $query->whereNotIn('state', [HistoryState::Error->value])
                    ->first()
                    ?->hash === $history->hash;
        }

        /** @var History $el */
        $el = $query->where('created_at', '>=', $dateFrom->format('Y-m-d H:i:s'))
            ->where('hash', '=', $history->hash)
            ->first();

        return !$el?->exists || $el->state === HistoryState::Error->value;
    }
}
