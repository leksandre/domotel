<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Services\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

interface DownloadService
{
    public function __construct(LoggerInterface $logger, Filesystem $storage, ?string $dirPath = null);

    public function download(string $url, ?string $fileName = null): ?array;
}
