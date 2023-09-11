<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase;

use Kelnik\EstateImport\PreProcessor\Contracts\Filter;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;

final class ParamsDto
{
    public ?Contracts\ProfitBaseClient $client = null;
    public ?MapperDto $mapperDto;
    public ?Filter $filter;
}
