<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesStatus;

interface PremisesStatusRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): PremisesStatus;

    public function getListForStat(): Collection;

    public function getListWithCardAvailable(): Collection;
}
