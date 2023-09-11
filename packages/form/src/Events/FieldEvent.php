<?php

declare(strict_types=1);

namespace Kelnik\Form\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\Form\Models\Field;

final class FieldEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public readonly Field $field, public string $methodName)
    {
    }
}
