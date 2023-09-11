<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\EstateImport\Models\History;

final class RemoveHistoryData implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(protected History $history)
    {
    }

    public function handle(): void
    {
        $className = $this->history->pre_processor;

        (new $className($this->history))->clean();
    }

    public function uniqueId()
    {
        return $this->history->getKey();
    }
}
