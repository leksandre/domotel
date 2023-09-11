<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Repositories\Contracts\BuildingRepository;

final class BuildingEloquentRepository extends EstateEloquentRepository implements BuildingRepository
{
    protected string $modelNamespace = Building::class;

    public function findByPrimary(int|string $primary): Building
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getAdminList(): LengthAwarePaginator
    {
        return $this->modelNamespace::filters()
            ->defaultSort('priority', 'asc')
            ->adminList()
            ->withCount(['sections', 'floors'])
            ->paginate();
    }

    public function getAllByComplex(int|string $complexPrimary): Collection
    {
        return $this->modelNamespace::select(['id', 'title'])->where('complex_id', '=', $complexPrimary)->get();
    }

    public function getAllBySelectionForAdmin(string $selectionClassName): Collection
    {
        return $this->getBySelection($selectionClassName)->get();
    }

    public function getAllBySelectionForAdminPaginated(string $selectionClassName): Paginator
    {
        return $this->getBySelection($selectionClassName)->paginate();
    }

    private function getBySelection(string $selectionClassName): Builder
    {
        return $this->modelNamespace::filtersApplySelection($selectionClassName)
            ->with([
                'complex' => fn(BelongsTo $builder) => $builder->adminList()
            ])
            ->withCount(['sections', 'floors']);
    }
}
