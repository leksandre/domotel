<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Mortgage\Events\BankEvent;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Observers\Contracts\Observer;

final class BankObserver extends Observer
{
    /**
     * @param Bank $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        BankEvent::dispatch($model, $event);
    }
}
