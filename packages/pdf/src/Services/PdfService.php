<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Services;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Kelnik\Pdf\Drivers\Contracts\CompressorDriver;
use Kelnik\Pdf\Drivers\Contracts\GeneratorDriver;
use Kelnik\Pdf\Drivers\Compressors\Factory as CompressorFactory;
use Kelnik\Pdf\Drivers\Generators\Factory as GeneratorFactory;
use Kelnik\Pdf\Traits\Storage;
use Symfony\Component\Mime\MimeTypes;

final class PdfService implements Contracts\PdfService
{
    use Storage;

    private readonly GeneratorDriver $generator;
    private readonly ?CompressorDriver $compressor;
    private readonly Filesystem $storage;
    private ?string $folder;

    /** @throws Exception */
    public function __construct()
    {
        $this->initGeneratorDriver();
        $this->initCompressorDriver();
        $this->initStorage();
        $this->folder = config('kelnik-pdf.storage.folder');
    }

    /** @throws Exception */
    private function initGeneratorDriver(): void
    {
        $connection = config('kelnik-pdf.connection');
        $driverName = config('kelnik-pdf.connections.' . $connection . '.driver');

        if (!$connection || !$driverName) {
            throw new Exception('Driver required');
        }

        $this->generator = GeneratorFactory::make($driverName);
    }

    private function initCompressorDriver(): void
    {
        if (!config('kelnik-pdf.compress.enable') === true) {
            return;
        }

        $this->compressor = CompressorFactory::make(config('kelnik-pdf.compress.driver'));
    }

    public function printToBinary(string $html): string
    {
        return $this->compressBinary(
            $this->generator->printToBinary(
                $this->replaceLocalFileLinks($html)
            )
        );
    }

    public function printToBase64(string $html): string
    {
        return base64_encode($this->printToBinary($html));
    }

    public function printToFile(
        string $moduleName,
        string $filePath,
        string $html,
        array $cacheTags = []
    ): \Kelnik\Pdf\Services\Contracts\PdfFileResponse {
        $filePath = $this->getFullPath($moduleName, $filePath);
        $response = new PdfFileResponse($filePath, $this->storage);

        $html = $this->replaceLocalFileLinks($html);

        $this->generator->printToFile($html, $this->storage, $filePath);
        $this->compress($filePath);

        Cache::tags($cacheTags)->put($this->getCacheId($filePath), $filePath, config('kelnik-pdf.cache.expired'));

        return $response;
    }

    public function getFileByPath(string $moduleName, string $filePath): ?PdfFileResponse
    {
        $filePath = $this->getFullPath($moduleName, $filePath);
        $realFilePath = Cache::get($this->getCacheId($filePath));

        return $realFilePath && $this->hasActualFile($realFilePath)
            ? new PdfFileResponse($realFilePath, $this->storage)
            : null;
    }

    private function getFullPath(string $moduleName, string $filePath): string
    {
        $moduleName = Str::slug(trim($moduleName));
        $filePath = trim($filePath);

        if (!Str::length($filePath)) {
            throw new InvalidArgumentException('Module name or filePath is empty');
        }

        $filePath = $moduleName . '/' . $filePath;

        if (!$this->folder) {
            return $filePath;
        }

        return rtrim($this->folder, '/') . '/' . ltrim($filePath, '/');
    }

    private function hasActualFile(string $filePath): bool
    {
        $config = config('kelnik-pdf.cache');

        return $this->storage->exists($filePath)
            && $this->storage->lastModified($filePath) > now()->subSeconds($config['expired'] ?? 0)->getTimestamp();
    }

    private function compress(string $filePath): bool
    {
        return $this->compressor?->compressFile($this->storage, $filePath, $filePath) ?? false;
    }

    private function compressBinary(string $data): string
    {
        return $this->compressor?->compressBinary($data) ?? $data;
    }

    private function replaceLocalFileLinks(string $html): string
    {
        $mimeTypes = new MimeTypes();

        return preg_replace_callback(
            [
                '!src="([^"]+)"!i',
                '!url\(\'?([^)]+)\'?\)!i'
            ],
            static function ($matches) use ($mimeTypes) {
                $filePath = $matches[1] ?? '';

                if (!$filePath || filter_var($filePath, FILTER_VALIDATE_URL)) {
                    return $matches[0];
                }

                $filePath = public_path($filePath);

                if (!is_readable($filePath)) {
                    return $matches[0];
                }

                $mimeType = current(
                    $mimeTypes->getMimeTypes(pathinfo($filePath, PATHINFO_EXTENSION))
                );

                if (!$mimeType) {
                    return $matches[0];
                }

                return str_replace(
                    $matches[1],
                    'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($filePath)),
                    $matches[0]
                );
            },
            $html
        );
    }

    private function getCacheId(string $filePath): string
    {
        return 'pdf_' . md5($filePath);
    }
}
