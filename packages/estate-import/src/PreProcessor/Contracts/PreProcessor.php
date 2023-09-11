<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\PreProcessor\Contracts;

use Illuminate\Support\Facades\Cache;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\PreProcessor\IsNotUniqueException;
use Kelnik\EstateImport\Processor\Contracts\BaseProcessor;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;

abstract class PreProcessor extends BaseProcessor
{
    protected ImportSettingsService $importSettingsService;
    protected array $rows = [];

    public function __construct(History $history)
    {
        $this->importSettingsService = resolve(ImportSettingsService::class);
        parent::__construct($history);
    }

    public function prepareData(...$data): bool
    {
        $this->history->pre_processor = static::class;
        $this->history->hash = md5(date('Y-m-d H:i'));

        return $this->historyRepository->save($this->history);
    }

    /** @throws IsNotUniqueException */
    public function checkUnique(): bool
    {
        $isUnique = $this->historyRepository->hasSameHash(
            $this->history,
            now()->subSeconds(config('kelnik-estate-import.unique.dateFrom'))
        );

        if (!$isUnique) {
            $this->logger->error('Data hash is not unique. Process is stopped.');
            throw new IsNotUniqueException(trans('kelnik-estate-import::admin.history.errors.notUnique'));
        }

        return true;
    }

    abstract public function execute(): bool;

    public function clean(): bool
    {
        $this->resetCache();

        $dirName = $this->getHistoryDirName();

        if (!$dirName || !$this->storage->exists($dirName)) {
            return false;
        }

        return $this->storage->deleteDirectory($dirName);
    }

    protected function addRow(string $name, array $data): void
    {
        Cache::tags($this->getCacheTag())->put(
            $this->getCacheKey($name, $data['external_id'] ?? ''),
            $data
        );

        $this->rows[] = [
            'history_id' => $this->history->getKey(),
            'model' => $name,
            'data' => $data
        ];
    }

    protected function rowExists(string $name, array $data): bool
    {
        return Cache::has($this->getCacheKey($name, $data['external_id'] ?? ''));
    }

    protected function flushRows(): void
    {
        $this->logger->debug('Add rows to DB', ['cnt' => count($this->rows), 'rows' => $this->rows]);
        $this->dataQueueRepository->saveMulti($this->rows);
        $this->rows = [];
    }

    protected function resetCache(): bool
    {
        $this->logger->info('Deleting cache');

        return Cache::tags($this->getCacheTag())->flush();
    }

    protected function getCacheKey(string $name, string|int $externalId): string
    {
        return 'history_' . $this->history->getKey() . '_' . md5($name . '_' . $externalId);
    }
}
