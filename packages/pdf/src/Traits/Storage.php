<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Traits;

trait Storage
{
    protected function initStorage(): void
    {
        $storageConfig = config('kelnik-pdf.storage.config');

        $this->storage = is_array($storageConfig) && $storageConfig
            ? \Illuminate\Support\Facades\Storage::build($storageConfig)
            : \Illuminate\Support\Facades\Storage::disk(config('kelnik-pdf.storage.disk'));
    }
}
