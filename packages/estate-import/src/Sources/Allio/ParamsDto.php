<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio;

use Kelnik\EstateImport\PreProcessor\Contracts\Filter;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;

final class ParamsDto
{
    public ?Contracts\AllioClient $client = null;
    public ?MapperDto $mapperDto;
    public ?Filter $filter;
}
