<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Kelnik\Pdf\Jobs\DeleteOldFiles;

final class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            if ($config = config('kelnik-pdf.schedule.cleaner')) {
                resolve(Schedule::class)->job(new DeleteOldFiles())->cron($config);
            }
        });
    }
}
