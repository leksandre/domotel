<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Commands;

use Illuminate\Console\Command;
use Kelnik\EstateImport\Jobs\ClearingModuleData;

final class CleanModule extends Command
{
    /** @var string */
    protected $signature = 'kelnik:estate-import.clean';

    /** @var string */
    protected $description = 'Clears the real estate import database';

    public function handle(): int
    {
        ClearingModuleData::dispatchSync();

        $this->info('Done');

        return Command::SUCCESS;
    }
}
