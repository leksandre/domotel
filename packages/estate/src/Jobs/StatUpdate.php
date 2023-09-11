<?php

declare(strict_types=1);

namespace Kelnik\Estate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kelnik\Estate\Services\Contracts\StatService;

final class StatUpdate implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        resolve(StatService::class)->run();
    }

    public function uniqueId(): string
    {
        return 'estate_module_stat';
    }
}
