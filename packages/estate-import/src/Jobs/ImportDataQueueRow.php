<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Kelnik\EstateImport\Jobs\Contracts\HistoryJob;
use Kelnik\EstateImport\Models\DataQueue;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\Contracts\EstateModelProxy;
use Kelnik\EstateImport\Repositories\Contracts\DataQueueRepository;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;
use Throwable;

final class ImportDataQueueRow extends HistoryJob implements ShouldBeUnique
{
    use Batchable;

    protected ?LoggerInterface $logger;
    protected ?Filesystem $storage;

    public function __construct(protected History $history, private DataQueue $dataQueue)
    {
        parent::__construct($this->history);
    }

    /** @throws Throwable */
    public function handle(): void
    {
        /** @var DataQueueRepository $repository */
        $repository = resolve(DataQueueRepository::class);

        $this->initStorage();
        $this->initLogger();

        $batch = $this->batch();

        if ($batch->pendingJobs === $batch->totalJobs) {
            $this->history->batch_id = $batch->id;
            resolve(HistoryRepository::class)->save($this->history);
        }

        /** @var EstateModelProxy $estateModelProxy */
        try {
            $className = $this->dataQueue->model;
            $estateModelProxy = new $className(
                $this->history,
                resolve(CacheService::class, ['history' => $this->history]),
                $this->logger,
                $this->storage
            );
            $estateModelProxy->setData($this->dataQueue->data);
            $estateModelProxy->import();

            $this->dataQueue->setEventDeclined();

            if ($estateModelProxy->isAdded()) {
                $this->dataQueue->setEventAdded();
            } elseif ($estateModelProxy->isUpdated()) {
                $this->dataQueue->setEventUpdated();
            }

            $this->dataQueue->done = true;
            $repository->save($this->dataQueue);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), $this->dataQueue->toArray());
            $this->dataQueue->setEventDeclined();
            $repository->save($this->dataQueue);
            throw $e;
        }
    }

    public function uniqueId()
    {
        return $this->dataQueue->getKey();
    }
}
