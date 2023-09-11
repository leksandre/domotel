<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Contracts;

interface ClientBase
{
    public function checkConnection(): bool;
}
