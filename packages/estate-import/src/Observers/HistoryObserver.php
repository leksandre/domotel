<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Observers;

use Kelnik\EstateImport\Events\HistoryModelEvent;
use Kelnik\EstateImport\Models\History;

final class HistoryObserver
{
    public function created(History $history): void
    {
        $this->eventHandler($history, __FUNCTION__);
    }

    public function updated(History $history): void
    {
        $this->eventHandler($history, __FUNCTION__);
    }

    public function deleted(History $history): void
    {
        $this->eventHandler($history, __FUNCTION__);
    }

    public function forceDelete(History $history): void
    {
        $this->eventHandler($history, __FUNCTION__);
    }

    protected function eventHandler(History $history, string $methodName): void
    {
        HistoryModelEvent::dispatch($history, $methodName);
    }
}
