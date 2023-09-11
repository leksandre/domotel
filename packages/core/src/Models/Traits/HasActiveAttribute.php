<?php

declare(strict_types=1);

namespace Kelnik\Core\Models\Traits;

trait HasActiveAttribute
{
    public function isActive(): bool
    {
        return $this->active === true;
    }
}
