<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\EstateImport\Models\DataQueue;
use Kelnik\EstateImport\Models\History;

interface DataQueueRepository
{
    /**
     * @param DataQueue[]|array $rows
     *
     * @return int
     */
    public function saveMulti(array $rows): int;

    public function save(DataQueue $dataQueue): bool;

    public function getLazyCollection(History $history): LazyCollection;

    public function getStatByHistory(History $history): Collection;

    public function forceDeleteByHistory(History $history): bool;
}
