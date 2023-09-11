<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio\Contracts;

use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;

interface HasMultiValues
{
    public function getElements(MapperDto $dto): array;
}
