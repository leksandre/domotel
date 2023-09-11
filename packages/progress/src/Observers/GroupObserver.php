<?php

declare(strict_types=1);

namespace Kelnik\Progress\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Progress\Events\GroupEvent;
use Kelnik\Progress\Models\Group;
use Kelnik\Progress\Observers\Contracts\Observer;

final class GroupObserver extends Observer
{
    /**
     * @param Group $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        GroupEvent::dispatch($model, $event);
    }
}
