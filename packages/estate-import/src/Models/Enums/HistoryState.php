<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Enums;

enum HistoryState: string
{
    case New = 'new';                 // New task
    case PreProcess = 'pre-process';  // Pre-processing is running
    case Ready = 'ready';             // Pre-processing is done, ready for processing
    case Process = 'process';         // Processing is running
    case Done = 'done';               // Import is completely done
    case Error = 'error';             // Finished with error

    public function isNew(): bool
    {
        return $this === self::New;
    }

    public function isReady(): bool
    {
        return $this === self::Ready;
    }

    public function isDone(): bool
    {
        return $this === self::Done;
    }

    public function isError(): bool
    {
        return $this === self::Error;
    }

    public function canRunProcessing(): bool
    {
        return $this->isNew() || $this->isReady();
    }

    public function isPreProcessing(): bool
    {
        return $this === HistoryState::PreProcess;
    }

    public function isProcessing(): bool
    {
        return $this === HistoryState::Process;
    }

    public function isInProgress(): bool
    {
        return $this->isPreProcessing() || $this->isProcessing() || $this->isReady();
    }
}
