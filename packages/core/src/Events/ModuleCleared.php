<?php

declare(strict_types=1);

namespace Kelnik\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class ModuleCleared
{
    use Dispatchable;

    public function __construct(public readonly string $moduleName)
    {
    }
}
