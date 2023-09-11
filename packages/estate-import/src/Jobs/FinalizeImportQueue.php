<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Jobs;

use Kelnik\Estate\Jobs\StatUpdate;
use Kelnik\EstateImport\Jobs\Contracts\HistoryJob;
use Kelnik\EstateImport\Models\DataQueue;
use Kelnik\EstateImport\Models\Enums\DataQueueEvent;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\DataQueueRepository;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;

final class FinalizeImportQueue extends HistoryJob
{
    public function handle(): void
    {
        /** @var HistoryRepository $historyRepository */
        $historyRepository = resolve(HistoryRepository::class);

        $this->initStorage();
        $this->initLogger();
        $this->history->setResultForState([
            'time' => [
                'finish' => now()->getTimestamp()
            ]
        ]);

        $this->history->setStateIsDone();
        $this->history->setResultForState([
            'time' => [
                'start' => now()->getTimestamp()
            ],
            'stat' => $this->getResultStat($this->history)
        ]);

        $this->logger->info('Delete import cache');
        resolve(CacheService::class, ['history' => $this->history])->flush();
        $historyRepository->save($this->history);

        $this->logger->info('Run estate stat update');
        StatUpdate::dispatchSync();

        $this->logger->info('Import is complete');
    }

    private function getResultStat(History $history): array
    {
        $res = resolve(DataQueueRepository::class)
            ->getStatByHistory($history)
            ->pluck(null, 'model')
            ->map(static fn(DataQueue $el) => [
                'added' => $el->getAttribute('added'),
                'updated' => $el->getAttribute('updated')
            ])
            ->toArray();

        // Get additional stat info from history cache (etc. Attachments, PremisesFeatures)
        $cacheRes = resolve(CacheService::class, ['history' => $this->history])->getStat();

        if (!$cacheRes) {
            return $res;
        }

        $cacheRes = array_map(static fn(array $el) => [
            'added' => $el[DataQueueEvent::Added->value] ?? 0,
            'updated' => $el[DataQueueEvent::Updated->value] ?? 0
        ], $cacheRes);

        return array_merge($res, $cacheRes);
    }
}
