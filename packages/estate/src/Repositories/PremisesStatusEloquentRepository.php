<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;

final class PremisesStatusEloquentRepository extends EstateEloquentRepository implements PremisesStatusRepository
{
    protected string $modelNamespace = PremisesStatus::class;

    public function findByPrimary(int|string $primary): PremisesStatus
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getListForStat(): Collection
    {
        return $this->modelNamespace::select(['id'])->where('take_stat', true)->get();
    }

    public function getListWithCardAvailable(): Collection
    {
        return $this->modelNamespace::select('*')->where('premises_card_available', true)->get();
    }
}
