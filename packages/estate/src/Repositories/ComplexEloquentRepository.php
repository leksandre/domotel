<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Repositories\Contracts\ComplexRepository;

final class ComplexEloquentRepository extends EstateEloquentRepository implements ComplexRepository
{
    protected string $modelNamespace = Complex::class;

    public function findByPrimary(int|string $primary): Complex
    {
        return $this->modelNamespace::findOrNew($primary);
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
        return $this->modelNamespace::filters($selectionClassName)->adminList()->withCount('buildings');
    }
}
