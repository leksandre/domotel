<?php

declare(strict_types=1);

namespace Kelnik\Estate\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Kelnik\Estate\Jobs\UpdatePlanoplanData;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;

final class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            if ($config = config('kelnik-estate.planoplan.update.schedule')) {
                resolve(Schedule::class)
                    ->job(new UpdatePlanoplanData(resolve(PlanoplanRepository::class)))
                    ->cron($config);
            }
        });
    }
}
