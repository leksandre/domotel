<?php

declare(strict_types=1);

namespace Kelnik\Contact\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Contact\Events\OfficeEvent;
use Kelnik\Contact\Models\Office;
use Kelnik\Contact\Observers\Contracts\Observer;

final class OfficeObserver extends Observer
{
    /**
     * @param Office $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        OfficeEvent::dispatch($model, $event);
    }
}
