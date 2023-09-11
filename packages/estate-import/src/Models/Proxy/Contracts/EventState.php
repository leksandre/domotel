<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy\Contracts;

interface EventState
{
    public function isAdded(): bool;

    public function isUpdated(): bool;
}
