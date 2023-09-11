<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Estate;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesStatusRepository;

final class PremisesStatusEloquentRepository implements PremisesStatusRepository
{
    protected string $modelNamespace = PremisesStatus::class;

    public function getList(): Collection
    {
        return $this->modelNamespace::select(['id', 'premises_card_available', 'hide_price', 'title'])
            ->orderBy('priority')
            ->orderBy('title')
            ->get();
    }
}
