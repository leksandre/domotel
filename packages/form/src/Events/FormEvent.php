<?php

declare(strict_types=1);

namespace Kelnik\Form\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Form\Models\Form;

final class FormEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public readonly Form $form, public string $methodName)
    {
    }
}
