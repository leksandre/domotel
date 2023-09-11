<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Contracts;

abstract class ProfitBaseConfig
{
    public string $apiUrl = '';
    public string $apiKey = '';
    public int $connectionTimeOut = 3;
}
