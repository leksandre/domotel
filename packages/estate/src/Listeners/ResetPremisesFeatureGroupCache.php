<?php

declare(strict_types=1);

namespace Kelnik\Estate\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Events\EstateModelEvent;
use Kelnik\Estate\Models\PremisesFeatureGroup;

final class ResetPremisesFeatureGroupCache implements ShouldQueue
{
    public function handle(EstateModelEvent $event): void
    {
        if (!$event->modelData instanceof PremisesFeatureGroup) {
            return;
        }

        Cache::forget(PremisesFeatureGroup::CACHE_GENERAL_GROUP_ID);
    }
}
