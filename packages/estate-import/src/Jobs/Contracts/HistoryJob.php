<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Jobs\Contracts;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Traits\Logger;
use Kelnik\EstateImport\Traits\Storage;
use Psr\Log\LoggerInterface;

abstract class HistoryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Logger;
    use Queueable;
    use SerializesModels;
    use Storage;

    protected ?LoggerInterface $logger;
    protected ?Filesystem $storage;

    public function __construct(protected History $history)
    {
        $this->onQueue(config('kelnik-estate-import.queue.name'));
    }
}
