<?php

declare(strict_types=1);

namespace Kelnik\Estate\Commands;

use Illuminate\Console\Command;
use Kelnik\Estate\Jobs\ClearingModuleData;

final class CleanModule extends Command
{
    /** @var string */
    protected $signature = 'kelnik:estate.clean';

    /** @var string */
    protected $description = 'Clears the real estate database';

    public function handle(): int
    {
        ClearingModuleData::dispatchSync();

        $this->info('Done');

        return Command::SUCCESS;
    }
}
