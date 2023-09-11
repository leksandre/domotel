<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

final class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            /** @var Schedule $schedule */
            $schedule = resolve(Schedule::class);

            foreach (config('kelnik-estate-import.schedule') as $jobClassName => $config) {
                if (!$config) {
                    continue;
                }

                $schedule->job(new $jobClassName())->cron($config);
            }
        });
    }
}
