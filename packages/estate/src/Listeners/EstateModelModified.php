<?php

declare(strict_types=1);

namespace Kelnik\Estate\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Events\EstateModelEvent;
use Kelnik\Estate\Jobs\ClearingModuleCache;
use Kelnik\Estate\Jobs\StatUpdate;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Services\Contracts\EstateService;

final class EstateModelModified implements ShouldQueue
{
    public function handle(EstateModelEvent $event): void
    {
        $runStatUpdater = $this->isDeleted($event)
            || $this->activeStateIsToggled($event)
            || $this->premisesIsUpdatedOrCreated($event);

        $estateService = resolve(EstateService::class);

        if ($event->modelData::class === Premises::class) {
            Cache::tags([
                $estateService->getPremisesCacheTag($event->modelData->getKey())
            ])->flush();
        }

        if ($runStatUpdater) {
            StatUpdate::dispatch();
            return;
        }

        ClearingModuleCache::dispatch();
    }

    private function isDeleted(EstateModelEvent $event): bool
    {
        return in_array($event->modelEvent, [$event::DELETED, $event::FORCE_DELETED]);
    }

    private function activeStateIsToggled(EstateModelEvent $event): bool
    {
        $activeModels = [Premises::class, Floor::class, Section::class, Building::class, Complex::class];

        return in_array($event->modelData::class, $activeModels) && $event->modelData->isDirty('active');
    }

    private function premisesIsUpdatedOrCreated(EstateModelEvent $event): bool
    {
        return in_array($event->modelEvent, [$event::UPDATED, $event::CREATED])
            && $event->modelData::class === Premises::class;
    }
}
