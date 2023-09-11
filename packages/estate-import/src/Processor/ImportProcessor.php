<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Processor;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\LazyCollection;
use Kelnik\EstateImport\Jobs\FinalizeImportQueue;
use Kelnik\EstateImport\Jobs\ImportDataQueueRow;
use Kelnik\EstateImport\Models\DataQueue;
use Kelnik\EstateImport\Processor\Contracts\Processor;

final class ImportProcessor extends Processor
{
    public function execute(): bool
    {
        $this->logger->info('Run import', ['class' => self::class]);
        $queueName = config('kelnik-estate-import.queue.name');
        $history = $this->history;

        $batch = Bus::batch([])
            ->allowFailures()
            ->finally(static fn() => FinalizeImportQueue::dispatch($history))
            ->name('Estate import data');

        if ($queueName) {
            $batch->onQueue($queueName);
        }

        $this->dataQueueRepository
            ->getLazyCollection($this->history)
            ->map(fn(DataQueue $el) => new ImportDataQueueRow($this->history, $el))
            ->chunk(100)
            ->each(static fn(LazyCollection $jobs) => $batch->add($jobs));

        $this->history->setResultForState([
            'time' => [
                'finish' => now()->getTimestamp()
            ]
        ]);
        $this->history->setStateIsProcess();
        $this->history->setResultForState([
            'time' => [
                'start' => now()->getTimestamp()
            ]
        ]);
        $this->historyRepository->save($this->history);

        $batch->dispatch();

        return true;
    }
}
