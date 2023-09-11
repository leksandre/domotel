<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Traits;

trait Storage
{
    protected function initStorage(): void
    {
        $this->storage = \Illuminate\Support\Facades\Storage::build(config('kelnik-estate-import.storage.config'));
    }
}
