<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\Core\Events\ModuleCleared;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Providers\EstateImportServiceProvider;

final class ClearingModuleData implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;

    public function handle(): void
    {
        History::query()->get()->each->delete();
        History::query()->truncate();

        ModuleCleared::dispatch(EstateImportServiceProvider::MODULE_NAME);
    }
}
