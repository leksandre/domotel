<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\PreProcessor\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

abstract class MapperDto
{
    public ?LoggerInterface $logger = null;
    public ?Filesystem $storage = null;
    public ?string $historyDirPath = null;
    public ?string $filesDirPath = null;

    public mixed $source = [];

    /** @var array Result array */
    public array $result = [];
}
