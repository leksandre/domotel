<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;

final class PlanoplanEloquentRepository implements PlanoplanRepository
{
    private string $modelNamespace = Planoplan::class;

    public function findByPrimary(int|string $primary): Planoplan
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getByPrimary(iterable $primaryKeys): Collection
    {
        return $this->modelNamespace::whereKey($primaryKeys)->get();
    }

    public function getOld(string|DateTime $edgeDate, int $limit = 0): LazyCollection
    {
        if ($edgeDate instanceof DateTime) {
            $edgeDate = $edgeDate->format('Y-m-d H:i:s');
        }

        return $this->modelNamespace::where('updated_at', '<=', $edgeDate)
            ->oldest('updated_at')
            ->limit($limit)
            ->cursor();
    }

    public function save(Planoplan $model): bool
    {
        return $model->save();
    }

    public function saveQuietly(Planoplan $model): bool
    {
        return $model->saveQuietly();
    }

    public function delete(Planoplan $model): bool
    {
        return $model->delete();
    }

    public function hasPremises(Planoplan $model): bool
    {
        return $model->exists && $model->premises()->limit(1)->exists();
    }
}
