<?php

declare(strict_types=1);

namespace Kelnik\Document\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Document\Events\GroupEvent;
use Kelnik\Document\Models\Group;
use Kelnik\Document\Observers\Contracts\Observer;

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
