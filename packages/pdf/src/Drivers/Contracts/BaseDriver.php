<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Contracts;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BaseDriver
{
    protected string $errorMsg = '';

    protected function logError(string $title, Process $process): void
    {
        Log::error(
            $title,
            [
                'code' => $process->getExitCode(),
                'msg' => $this->errorMsg ?? $process->getErrorOutput(),
                'command' => $process->getCommandLine()
            ]
        );
    }
}
