<?php

declare(strict_types=1);

namespace Kelnik\Estate\Observers;

use Kelnik\Estate\Events\PlanoplanEvent;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Contracts\EstateModelReference;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Observers\Contracts\Observer;

final class PlanoplanObserver extends Observer
{
    protected function handle(EstateModel|EstateModelReference|Planoplan $model, string $methodName): void
    {
        if ($model instanceof EstateModelReference) {
            return;
        }

        PlanoplanEvent::dispatch($model, $methodName);
    }
}
