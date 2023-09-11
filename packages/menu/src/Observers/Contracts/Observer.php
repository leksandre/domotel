<?php

declare(strict_types=1);

namespace Kelnik\Menu\Observers\Contracts;

use Illuminate\Database\Eloquent\Model;

abstract class Observer
{
    public function created(Model $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    public function updated(Model $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    public function deleted(Model $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    public function restored(Model $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    public function forceDeleted(Model $model): void
    {
        $this->handle($model, __FUNCTION__);
    }

    abstract protected function handle(Model $model, string $event);
}
