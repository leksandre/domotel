<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Contracts\Estate;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateImport\Repositories\Contracts\BaseLazyCollection;

abstract class BaseEloquentRepository implements BaseRepository, BaseLazyCollection
{
    protected string $modelNamespace;
    protected array $lazyCollectionFields = ['id', 'external_id', 'hash'];

    public function findByExternalIdOrNew(int|float|string $externalId): EstateModel
    {
        return $this->modelNamespace::where('external_id', $externalId)->firstOrNew();
    }

    public function getLazyCollection(): LazyCollection
    {
        return $this->modelNamespace::select($this->lazyCollectionFields)->cursor();
    }

    public function save(EstateModel $model): bool
    {
        return $model->save();
    }

    public function saveQuietly(EstateModel $model): bool
    {
        return $model->saveQuietly();
    }

    public function removeRelation(Relation $relation): mixed
    {
        if ($relation instanceof BelongsToMany) {
            return $relation->detach();
        }

        if ($relation instanceof HasManyThrough || $relation instanceof HasMany) {
            return $relation->delete();
        }

        return null;
    }

    public function syncRelation(Relation $relation, Collection $data): null|array|Collection
    {
        if ($relation instanceof BelongsToMany) {
            $pivotClass = $relation->getPivotClass();

            return $pivotClass::withoutEvents(fn() => $relation->sync($data->pluck('id')->toArray()));
        }

        if (!$relation instanceof HasMany) {
            return null;
        }

        $curValues = $relation->get();

        /**
         * @var EstateModel $curRow
         * @var EstateModel $newEl
         */
        foreach ($curValues as $curRow) {
            foreach ($data as $nk => $newEl) {
                if ($curRow->is($newEl)) {
                    $data->forget($nk);
                    continue 2;
                }
            }
            $curRow->deleteQuietly();
        }

        /** @var EstateModel $newEl */
        foreach ($data as $newEl) {
            $newEl->saveQuietly();
        }

        return $data;
    }
}
