<?php

declare(strict_types=1);

namespace Kelnik\Menu\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Menu\Events\MenuEvent;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Observers\Contracts\Observer;

final class MenuObserver extends Observer
{
    /**
     * @param Menu $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        MenuEvent::dispatch($model, $event);
    }
}
