<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio\Contracts;

abstract class AllioConfig
{
    public string $apiUrl = '';
    public string $apiLogin = '';
    public string $apiPassword = '';
    public int $apiDeveloper = 0;
    public int $connectionTimeOut = 3;
}
