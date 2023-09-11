<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kelnik\EstateImport\PreProcessor\Contracts\PreProcessor;
use Kelnik\EstateImport\PreProcessor\IsNotUniqueException;
use Kelnik\EstateImport\Processor\Contracts\Processor;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Throwable;

final class HistoryQueueProcessing implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    private HistoryRepository $repository;

    public function __construct()
    {
        $this->onQueue(config('kelnik-estate-import.queue.name'));
    }

    public function handle(): void
    {
        $this->repository = resolve(HistoryRepository::class);
        $history = $this->repository->getQueueRow();

        if (!$history->exists || !$history->state->canRunProcessing()) {
            return;
        }

        try {
            if ($history->state->isNew()) {
                /** @var PreProcessor $preProcessor */
                $className = $history->pre_processor;
                $preProcessor = new $className($history);

                $preProcessor->execute();

                return;
            }

            resolve(CacheService::class, ['history' => $history])->flush();

            /** @var Processor $processor */
            $processor = resolve(Processor::class, ['history' => $history]);
            $processor->execute();
        } catch (IsNotUniqueException $e) {
            $history->setResultForState([
                'time' => [
                    'finish' => now()->getTimestamp()
                ]
            ]);
            $history->setStateIsDone();
            $history->setResultForState([
                'time' => [
                    'start' => now()->getTimestamp()
                ],
                'message' => $e->getMessage()
            ]);
            $this->repository->save($history);
        } catch (Throwable $e) {
            $history->setResultForState([
                'time' => [
                    'finish' => now()->getTimestamp()
                ]
            ]);
            $history->setStateIsError();
            $history->setResultForState([
                'time' => [
                    'start' => now()->getTimestamp()
                ],
                'message' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine()
            ]);
            $this->repository->save($history);
        }
    }
}
