<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Mortgage\Events\ProgramEvent;
use Kelnik\Mortgage\Models\Program;
use Kelnik\Mortgage\Observers\Contracts\Observer;

final class ProgramObserver extends Observer
{
    /**
     * @param Program $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        ProgramEvent::dispatch($model, $event);
    }
}
