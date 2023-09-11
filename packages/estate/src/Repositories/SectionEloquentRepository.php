<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Repositories\Contracts\SectionRepository;

final class SectionEloquentRepository extends EstateEloquentRepository implements SectionRepository
{
    protected string $modelNamespace = Section::class;

    public function findByPrimary(int|string $primary): Section
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getAdminList(): LengthAwarePaginator
    {
        return $this->modelNamespace::filters()
            ->defaultSort('priority', 'asc')
            ->adminList()
            ->withCount('premises')
            ->paginate();
    }

    public function getAllByBuilding(int|string $buildingPrimary): Collection
    {
        return $this->modelNamespace::select(['id', 'title'])->where('building_id', '=', $buildingPrimary)->get();
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
            ->adminList()
            ->withCount('premises');
    }
}
