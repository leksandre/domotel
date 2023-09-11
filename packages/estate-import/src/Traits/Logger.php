<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Traits;

use Illuminate\Support\Facades\Log;

trait Logger
{
    protected function initLogger(): void
    {
        $this->logger = Log::build(config('kelnik-estate-import.logging.config'));
        $this->logger->withContext(['historyId' => $this->history->getKey()]);
    }
}
