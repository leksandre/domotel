<?php

declare(strict_types=1);

namespace Kelnik\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Models\Setting;

final class SettingUpdated
{
    use Dispatchable;

    public function __construct(public readonly Setting $setting)
    {
    }
}
