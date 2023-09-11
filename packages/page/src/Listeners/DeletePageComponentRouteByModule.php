<?php

declare(strict_types=1);

namespace Kelnik\Page\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Kelnik\Core\Events\ModuleCleared;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteElementRepository;

final class DeletePageComponentRouteByModule implements ShouldQueue
{
    public function handle(ModuleCleared $event): void
    {
        resolve(PageComponentRouteElementRepository::class)->deleteByModule($event->moduleName);
    }
}
