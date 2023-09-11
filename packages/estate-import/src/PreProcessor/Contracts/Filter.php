<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\PreProcessor\Contracts;

use Illuminate\Support\MessageBag;

interface Filter
{
    public function __invoke(string $modelName, array $data): bool|MessageBag;
}
