<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Services;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\BaseLazyCollection;
use Orchid\Attachment\Models\Attachment;

final class CacheService implements Contracts\CacheService
{
    private Repository $cache;

    public function __construct(protected History $history)
    {
        $this->cache = Cache::store(config('kelnik-estate-import.cache.store', 'file'));
    }

    public function hasModelList(string $modelNamespace): bool
    {
        return $this->cache->has($this->getModelCacheKey($modelNamespace));
    }

    public function cacheModelList(
        string $modelNamespace,
        BaseLazyCollection $repository,
        string $keyName = 'external_id'
    ): bool {
        return $this->cache->tags($this->getCacheTag())
            ->put(
                $this->getModelCacheKey($modelNamespace),
                $repository->getLazyCollection()->pluck(null, $keyName)->all()
            );
    }

    public function getModel(string $modelNamespace, int|float|string $keyValue): null|EstateModel|Attachment
    {
        $lock = $this->restore();
        $models = $this->cache->get($this->getModelCacheKey($modelNamespace), []);
        $lock->release();

        if (!$models || empty($models[$keyValue])) {
            return null;
        }

        return $models[$keyValue];
    }

    public function addModel(EstateModel|Attachment $model, string $keyName = 'external_id'): void
    {
        $cacheKey = $this->getModelCacheKey($model::class);
        $lock = $this->restore();

        $models = $this->cache->get($cacheKey, []);
        $models[$model->getAttribute($keyName)] = $model;
        $this->cache->tags($this->getCacheTag())->put($cacheKey, $models);

        $lock->release();

        unset($models, $model, $lock);
    }

    public function addStat(string $modelNamespace, string|int $event): void
    {
        $lock = $this->restore();
        $stat = $this->cache->get($this->getStatKey());
        $stat[$modelNamespace][$event] ??= 0;
        $stat[$modelNamespace][$event]++;
        $this->cache->tags($this->getCacheTag())->put($this->getStatKey(), $stat);
        $lock->release();
    }

    public function getStat(): array
    {
        $lock = $this->restore();
        $data = $this->cache->get($this->getStatKey(), []);
        $lock->release();

        return $data;
    }

    public function flush(): bool
    {
        return $this->cache->tags($this->getCacheTag())->flush();
    }

    private function restore(): Lock
    {
        return $this->cache->restoreLock($this->getCacheLockName(), $this->getCacheOwner());
    }

    private function lock(): Lock
    {
        return $this->cache->lock(
            $this->getCacheLockName(),
            config('kelnik-estate-import.processTimeOut'),
            $this->getCacheOwner()
        );
    }

    private function getCacheLockName(): string
    {
        return 'historyImport' . $this->history->getKey();
    }

    private function getCacheOwner(): string
    {
        return 'history' . $this->history->getKey();
    }

    private function getModelCacheKey(string $modelNamespace): string
    {
        return 'history' . $this->history->getKey() . '_' . $modelNamespace;
    }

    private function getCacheTag(): string
    {
        return $this->getCacheLockName();
    }

    private function getStatKey(): string
    {
        return 'historyStat' . $this->history->getKey();
    }
}
