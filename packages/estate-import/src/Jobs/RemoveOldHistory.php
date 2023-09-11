<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;

final class RemoveOldHistory implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        resolve(HistoryRepository::class)
            ->getCompletedRowsFromDate(now()->subDays(7))
            ?->each(static fn(History $el) => $el->delete());
    }
}
