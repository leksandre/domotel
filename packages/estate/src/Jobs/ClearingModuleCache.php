<?php

declare(strict_types=1);

namespace Kelnik\Estate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Services\Contracts\EstateService;

final class ClearingModuleCache implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 60;

    public function handle(): void
    {
        Cache::tags([resolve(EstateService::class)->getModuleCacheTag()])->flush();
    }

    public function uniqueId(): string
    {
        return 'estate_module_cache';
    }
}
