<?php

declare(strict_types=1);

namespace Kelnik\Estate\Observers\Contracts;

use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Contracts\EstateModelReference;
use Kelnik\Estate\Models\Planoplan;

abstract class Observer
{
    public function created(EstateModel|EstateModelReference|Planoplan $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    public function updated(EstateModel|EstateModelReference|Planoplan $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    public function deleted(EstateModel|EstateModelReference|Planoplan $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    public function restored(EstateModel|EstateModelReference|Planoplan $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    public function forceDeleted(EstateModel|EstateModelReference|Planoplan $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    abstract protected function handle(EstateModel|EstateModelReference|Planoplan $model, string $event);
}
