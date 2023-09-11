<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\Planoplan;

interface PlanoplanRepository
{
    public function findByPrimary(int|string $primary): Planoplan;

    public function getByPrimary(iterable $primaryKeys): Collection;

    public function getOld(string|DateTime $edgeDate, int $limit = 0): LazyCollection;

    public function save(Planoplan $model): bool;

    public function saveQuietly(Planoplan $model): bool;

    public function delete(Planoplan $model): bool;

    public function hasPremises(Planoplan $model): bool;
}
