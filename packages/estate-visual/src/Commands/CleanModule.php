<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Commands;

use Illuminate\Console\Command;
use Kelnik\EstateVisual\Jobs\ClearingModuleData;

final class CleanModule extends Command
{
    /** @var string */
    protected $signature = 'kelnik:estate-visual.clean';

    /** @var string */
    protected $description = 'Clears the real estate visual selector database';

    public function handle(): int
    {
        ClearingModuleData::dispatchSync();

        $this->info('Done');

        return Command::SUCCESS;
    }
}
