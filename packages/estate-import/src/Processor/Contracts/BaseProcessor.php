<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Processor\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Providers\EstateImportServiceProvider;
use Kelnik\EstateImport\Repositories\Contracts\DataQueueRepository;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Kelnik\EstateImport\Traits\Logger;
use Kelnik\EstateImport\Traits\Storage;
use Psr\Log\LoggerInterface;

abstract class BaseProcessor
{
    use Logger;
    use Storage;

    protected HistoryRepository $historyRepository;
    protected DataQueueRepository $dataQueueRepository;
    protected Filesystem $storage;
    protected LoggerInterface $logger;

    public function __construct(protected History $history)
    {
        $this->historyRepository = resolve(HistoryRepository::class);
        $this->dataQueueRepository = resolve(DataQueueRepository::class);
        $this->initStorage();
        $this->initLogger();
    }

    protected function getHistoryDirName(): ?string
    {
        return $this->history->getKey()
            ? $this->history->created_at->format('Ymd') . '_' . $this->history->getKey()
            : null;
    }

    protected function getCacheTag(): string
    {
        return EstateImportServiceProvider::MODULE_NAME . '_' . static::class;
    }
}
