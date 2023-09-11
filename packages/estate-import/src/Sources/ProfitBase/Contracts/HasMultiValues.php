<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Contracts;

use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;

interface HasMultiValues
{
    public function getElements(MapperDto $dto): array;
}
