<?php

declare(strict_types=1);

namespace Kelnik\Progress\Commands;

use Illuminate\Console\Command;
use Kelnik\Progress\Jobs\ClearingModuleData;

final class CleanModule extends Command
{
    /** @var string */
    protected $signature = 'kelnik:progress.clean';

    /** @var string */
    protected $description = 'Clears the progress database';

    public function handle(): int
    {
        ClearingModuleData::dispatchSync();

        $this->info('Done');

        return Command::SUCCESS;
    }
}
