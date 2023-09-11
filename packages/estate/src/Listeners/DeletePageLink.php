<?php

declare(strict_types=1);

namespace Kelnik\Estate\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Events\EstateModelEvent;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Page\Services\Contracts\PageLinkService;

final class DeletePageLink implements ShouldQueue
{
    public function handle(EstateModelEvent $event): void
    {
        if (
            $event->modelData::class !== PremisesTypeGroup::class
            || $event->modelEvent !== $event::DELETED
            || !resolve(CoreService::class)->hasModule('page')
        ) {
            return;
        }

        resolve(PageLinkService::class)->deletePageComponentRouteElements(
            $event->modelData::class,
            $event->modelData->getKey()
        );
    }
}
