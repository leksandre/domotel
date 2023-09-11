<?php

declare(strict_types=1);

namespace Kelnik\Document\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Document\Events\CategoryEvent;
use Kelnik\Document\Models\Category;
use Kelnik\Document\Observers\Contracts\Observer;

final class CategoryObserver extends Observer
{
    /**
     * @param Category $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        CategoryEvent::dispatch($model, $event);
    }
}
