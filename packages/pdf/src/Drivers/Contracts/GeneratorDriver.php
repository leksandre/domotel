<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\Pdf\Drivers\Generators\Config;

abstract class GeneratorDriver extends BaseDriver
{
    protected readonly Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $url URL or Html string
     * @return ?string Binary data or null
     */
    abstract public function printToBinary(string $url): ?string;

    /**
     * @param string $url
     * @return ?string Base64-encoded pdf data or null
     */
    public function printToBase64(string $url): ?string
    {
        return base64_encode($this->printToBinary($url));
    }

    public function printToFile(string $url, Filesystem $storage, string $path): bool
    {
        return $storage->put($path, $this->printToBinary($url));
    }

    protected function isUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    protected function convertInchToMm(float|int $val): int
    {
        return (int)ceil($val * 25.4);
    }
}
