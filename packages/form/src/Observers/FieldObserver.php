<?php

declare(strict_types=1);

namespace Kelnik\Form\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Form\Events\FieldEvent;
use Kelnik\Form\Models\Field;
use Kelnik\Form\Observers\Contracts\Observer;

final class FieldObserver extends Observer
{
    /**
     * @param Field $model
     * @param string $methodName
     * @return void
     */
    protected function handle(Model $model, string $methodName): void
    {
        FieldEvent::dispatch($model, $methodName);
    }
}
