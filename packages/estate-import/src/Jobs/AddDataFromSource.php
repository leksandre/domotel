<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Contracts\SourceType;
use Throwable;

final class AddDataFromSource
{
    use Dispatchable;

    public function handle(): void
    {
        try {
            /** @var SourceType $source */
            $source = resolve(ImportSettingsService::class)->getSource();
        } catch (Throwable $throwable) {
            Log::error('Init source class error', ['msg' => $throwable->getMessage()]);
            return;
        }

        if (!$source->canBeScheduled()) {
            return;
        }

        $source->runImport();
    }
}
