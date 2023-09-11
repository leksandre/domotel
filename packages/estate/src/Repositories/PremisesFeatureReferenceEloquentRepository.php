<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\PremisesFeatureReference;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureReferenceRepository;

final class PremisesFeatureReferenceEloquentRepository implements PremisesFeatureReferenceRepository
{
    /** @var PremisesFeatureReference $modelNamespace */
    protected string $modelNamespace = PremisesFeatureReference::class;

    public function findByPrimary(int|string $primary): PremisesFeatureReference
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getAll(array $fields = []): Collection
    {
        $query = $this->modelNamespace::query();

        if ($fields) {
            $query->select($fields);
        }

        return $query->get();
    }

    public function getAllLazy(): LazyCollection
    {
        return $this->modelNamespace::cursor();
    }

    public function deleteManyByPrimary(iterable $primaryKeys): int
    {
        return $this->modelNamespace::whereKey('id', $primaryKeys)->delete();
    }

    public function addMany(iterable $entries): int
    {
        return $this->modelNamespace::upsert($entries, ['premises_id', 'feature_id'], []);
    }
}
