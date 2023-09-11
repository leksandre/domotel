<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Estate;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Complex;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\ComplexRepository;

final class ComplexEloquentRepository implements ComplexRepository
{
    protected string $modelNamespace = Complex::class;

    public function findByPrimary(int|string $primary): Complex
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getForAdminByComplexPrimary(int|string $complexPrimary): Collection
    {
        return $complexPrimary
            ? $this->modelNamespace::whereKey($complexPrimary)
                ->orderBy('priority')
                ->orderBy('title')
                ->get(['id', 'title'])
            : new Collection();
    }
}
