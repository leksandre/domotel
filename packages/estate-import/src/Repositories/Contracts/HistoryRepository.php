<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Contracts;

use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;
use Kelnik\EstateImport\Models\History;

interface HistoryRepository
{
    public function findByPrimary(int|string $primary): History;

    public function getAdminList(): LengthAwarePaginator;

    public function save(History $model): bool;

    public function getQueueRow(): History;

    public function getCompletedRowsFromDate(DateTimeInterface $dateFrom): LazyCollection;

    public function hasSameHash(History $history, ?DateTimeInterface $dateFrom): bool;
}
