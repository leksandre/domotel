<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Contracts;

use Psr\Log\LoggerInterface;

interface HasClient
{
    public function getClient(?array $params = null, ?LoggerInterface $logger = null): ClientBase;
}
