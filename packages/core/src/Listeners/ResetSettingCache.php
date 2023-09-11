<?php

declare(strict_types=1);

namespace Kelnik\Core\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kelnik\Core\Events\SettingUpdated;
use Kelnik\Core\Services\Contracts\SettingsService;

final class ResetSettingCache implements ShouldQueue, ShouldBeUnique
{
    public function handle(SettingUpdated $event): void
    {
        resolve(SettingsService::class)
            ->resetSettingCache($event->setting->module, $event->setting->name);
    }
}
