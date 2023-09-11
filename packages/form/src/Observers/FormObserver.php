<?php

declare(strict_types=1);

namespace Kelnik\Form\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Form\Events\FormEvent;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Observers\Contracts\Observer;

final class FormObserver extends Observer
{
    /**
     * @param Form $model
     * @param string $methodName
     * @return void
     */
    protected function handle(Model $model, string $methodName): void
    {
        FormEvent::dispatch($model, $methodName);
    }
}
