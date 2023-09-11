<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\Pdf\Traits\Storage;

final class DeleteOldFiles implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use Storage;

    private Filesystem $storage;

    public function handle(): void
    {
        $this->initStorage();
        $expiredDate = now()->subSeconds(config('kelnik-pdf.cache.expired'))->getTimestamp();

        foreach ($this->storage->listContents('', true) as $el) {
            if (!$el->isFile() || $el->lastModified() > $expiredDate) {
                continue;
            }

            $this->storage->delete($el->path());
        }
    }
}
