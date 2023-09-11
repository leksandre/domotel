<?php

declare(strict_types=1);

namespace Kelnik\Document\Commands;

use Illuminate\Console\Command;
use Kelnik\Document\Jobs\ClearingModuleData;

final class CleanModule extends Command
{
    /** @var string */
    protected $signature = 'kelnik:document.clean';

    /** @var string */
    protected $description = 'Clears the document database';

    public function handle(): int
    {
        ClearingModuleData::dispatchSync();

        $this->info('Done');

        return Command::SUCCESS;
    }
}
