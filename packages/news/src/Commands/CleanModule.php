<?php

declare(strict_types=1);

namespace Kelnik\News\Commands;

use Illuminate\Console\Command;
use Kelnik\News\Jobs\ClearingModuleData;

final class CleanModule extends Command
{
    /** @var string */
    protected $signature = 'kelnik:news.clean';

    /** @var string */
    protected $description = 'Clears the news database';

    public function handle(): int
    {
        ClearingModuleData::dispatchSync();

        $this->info('Done');

        return Command::SUCCESS;
    }
}
