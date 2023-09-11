<?php

declare(strict_types=1);

namespace Kelnik\Document\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Document\Events\ElementEvent;
use Kelnik\Document\Models\Element;
use Kelnik\Document\Observers\Contracts\Observer;

final class ElementObserver extends Observer
{
    /**
     * @param Element $model
     * @param string $methodName
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        ElementEvent::dispatch($model, $event);
    }
}
