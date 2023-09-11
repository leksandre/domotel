<?php

declare(strict_types=1);

namespace Kelnik\Core\Providers\Contracts;

use Illuminate\Support\ServiceProvider;
use Kelnik\Document\Jobs\ClearingModuleData;

abstract class KelnikModule extends ServiceProvider
{
    public const VERSION = '0.0.0';

    abstract public function getName(): string;

    abstract public function getTitle(): string;

    public function hasCleaner(): bool
    {
        return false;
    }

    /** @return class-string[] */
    public function getCleanerJobs(): array
    {
        return [ClearingModuleData::class];
    }
}
