<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;

final class DownloadService implements Contracts\DownloadService
{
    public function __construct(
        private LoggerInterface $logger,
        private Filesystem $storage,
        private ?string $dirPath = null
    ) {
    }

    public function download(string $url, ?string $fileName = null): ?array
    {
        if (!$url) {
            return $this->getResult('');
        }

        $urlScheme = parse_url($url, PHP_URL_SCHEME);

        if (!$urlScheme) {
            return $this->getResult(
                $this->getFilePath($url)
            );
        }

        if (!in_array($urlScheme, ['http', 'https', 'ftp', 'ftps', 'sftp'])) {
            $this->logger->warning('Unsupported url scheme', [$urlScheme, $url]);

            return $this->getResult('');
        }

        $fileName ??= pathinfo($url, PATHINFO_BASENAME);

        if (!$fileName) {
            return $this->getResult('');
        }

        $filePath = $this->getFilePath($fileName);

        if ($this->storage->exists($filePath) && $this->getRemoteFileSize($url) === $this->storage->size($filePath)) {
            return $this->getResult($filePath);
        }

        if ($this->loadFile($url, $filePath)) {
            $this->logger->info('File downloaded');

            return $this->getResult(
                $this->checkMimeTypeAndExtension($filePath)
            );
        }

        $this->logger->error('Error on file download', ['url' => $url, 'error' => error_get_last()]);

        return $this->getResult('');
    }

    private function getResult(string $path): ?array
    {
        if (!$path) {
            return null;
        }

        if (!$this->storage->exists($path)) {
            $this->logger->warning('Local file not found', ['path' => $path]);

            return null;
        }

        return [
            'hash' => sha1($this->storage->get($path)),
            'path' => $path
        ];
    }

    private function loadFile(string $src, string $dst): bool
    {
        $this->logger->debug('Download file', ['src' => $src, 'dst' => $dst]);

        $srcStream = fopen($src, 'rb');
        $res = $this->storage->writeStream($dst, $srcStream);
        fclose($srcStream);

        return $res;
    }

    private function checkMimeTypeAndExtension(string $path): string
    {
        $mimeType = $this->storage->mimeType($path);
        $extByMime = (new MimeTypes())->getExtensions($mimeType);
        $fileExt = pathinfo($path, PATHINFO_EXTENSION);

        if (!$extByMime || !in_array(mb_strtolower($fileExt), $extByMime, true)) {
            return $path;
        }

        $extByMime = current($extByMime);

        $pathInfo = pathinfo($path);
        $newPath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.' . $extByMime;

        if ($newPath === $path) {
            return $path;
        }

        $this->logger->debug('Rename file', ['src' => $path, 'dst' => $newPath]);
        $this->storage->move($path, $newPath);

        return $newPath;
    }

    private function getRemoteFileSize(string $url): int
    {
        $cs = curl_init($url);

        curl_setopt_array(
            $cs,
            [
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_FOLLOWLOCATION => true
            ]
        );

        curl_exec($cs);
        $data = curl_getinfo($cs);
        curl_close($cs);

        return Arr::get($data, 'http_code') === Response::HTTP_OK
            ? (int)Arr::get($data, 'download_content_length', -1)
            : -1;
    }

    private function getFilePath(string $fileName): string
    {
        return $this->dirPath
            ? $this->dirPath . '/' . $fileName
            : $fileName;
    }
}
