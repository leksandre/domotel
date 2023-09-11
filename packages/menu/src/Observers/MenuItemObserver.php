<?php

declare(strict_types=1);

namespace Kelnik\Menu\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Menu\Events\MenuItemEvent;
use Kelnik\Menu\Models\MenuItem;
use Kelnik\Menu\Observers\Contracts\Observer;

final class MenuItemObserver extends Observer
{
    /**
     * @param MenuItem $model
     * @param string $methodName
     * @return void
     */
    protected function handle(Model $model, string $methodName): void
    {
        MenuItemEvent::dispatch($model, $methodName);
    }
}
