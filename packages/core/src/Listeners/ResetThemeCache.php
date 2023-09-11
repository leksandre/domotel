<?php

declare(strict_types=1);

namespace Kelnik\Core\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Events\SettingUpdated;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;
use Kelnik\Core\View\Components\Theme\GlobalTheme;

final class ResetThemeCache implements ShouldQueue, ShouldBeUnique
{
    public function handle(SettingUpdated $event): void
    {
        if (is_a(GlobalTheme::class, KelnikComponentCache::class, true)) {
            Cache::forget(resolve(GlobalTheme::class)->getCacheId());
        }
    }
}
