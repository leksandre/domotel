<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;

final class SelectorEloquentRepository extends BaseEloquentRepository implements SelectorRepository
{
    protected string $modelNamespace = Selector::class;

    public function findByPrimary(int|string $primary): Selector
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getActiveFirst(): Selector
    {
        return $this->modelNamespace::where('active', true)
            ->whereHas(
                'complex',
                static fn(Builder $builder) => $builder->select('id')->where('active', true)->limit(1)
            )
            ->firstOrNew();
    }

    public function getAllForAdminPaginated(): LengthAwarePaginator
    {
        return $this->modelNamespace::adminList()->with(['complex'])->paginate();
    }
}
