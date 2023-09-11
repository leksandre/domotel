<?php

declare(strict_types=1);

namespace Kelnik\Core\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Orchid\Attachment\File;
use Orchid\Attachment\Models\Attachment;
use Orchid\Platform\Events\UploadedFileEvent;

final class UploadService implements Contracts\UploadService
{
    private const TAG_PREFIX = 'coreFileUpload_';
    private const ITEM_PREFIX = 'coreFileChunk_';
    private const TMP_FILE_PREFIX = 'kelnik-upload-';
    private const CACHE_TTL = 3600; // 1h

    public function saveChunk(string $fileId, int $chunkIndex, UploadedFile $uploadedFile): bool
    {
        $fileName = self::TMP_FILE_PREFIX . Str::random(6) . '-' . $chunkIndex;
        $tmpFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;
        $fileData = [
            'fileName' => $uploadedFile->getClientOriginalName(),
            'mimeType' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
            'tmpPath' => $tmpFilePath
        ];

        $uploadedFile->move(sys_get_temp_dir(), $fileName);

        return Cache::tags($this->getCacheTag($fileId))->put(
            $this->getCacheElementName($fileId, $chunkIndex),
            [
                'index' => $chunkIndex,
                'file' => $fileData
            ],
            self::CACHE_TTL
        );
    }

    public function compileFileFromChunks(string $fileId, int $chunks, int $totalSize): ?UploadedFile
    {
        $files = [];

        for ($i = 0; $i < $chunks; $i++) {
            $files[] = Cache::tags($this->getCacheTag($fileId))->get($this->getCacheElementName($fileId, $i));
        }

        if (!$files) {
            return null;
        }

        $tmpFilePath = tempnam(sys_get_temp_dir(), self::TMP_FILE_PREFIX);
        $mimeType = null;
        $fileName = null;
        $error = false;
        $fp = fopen($tmpFilePath, 'ab');

        foreach ($files as $v) {
            if (!$mimeType) {
                $mimeType = $v['file']['mimeType'];
                $fileName = $v['file']['fileName'];
            }

            if (!is_readable($v['file']['tmpPath'])) {
                $error = true;
                break;
            }

            $tmpStream = fopen($v['file']['tmpPath'], 'rb');
            stream_copy_to_stream($tmpStream, $fp);
            fclose($tmpStream);
            unlink($v['file']['tmpPath']);
        }
        fclose($fp);

        Cache::tags($this->getCacheTag($fileId))->flush();

        return $error
            ? null
            : new UploadedFile($tmpFilePath, $fileName, $mimeType);
    }

    public function createAttachment(UploadedFile $file, string $storage, ?string $group): Attachment
    {
        $model = resolve(File::class, [
            'file'  => $file,
            'disk'  => $storage,
            'group' => $group,
        ])->load();

        $model->url = $model->url();

        event(new UploadedFileEvent($model));

        return $model;
    }

    public function getMaxUploadSize(): int
    {
        $iniParams = [
            'upload_max_filesize',
            'post_max_size',
            'memory_limit',
        ];
        $sizes = [];

        foreach ($iniParams as $param) {
            $sizes[] = $this->getValueInBytes(ini_get($param));
        }

        return (int)(min($sizes) * 0.9);
    }

    private function getValueInBytes(string $value): int
    {
        $lastSymbol = strtolower(substr($value, -1));
        $value = (int) $value;

        if (!strlen($lastSymbol) || is_numeric($lastSymbol)) {
            return $value;
        }

        switch ($lastSymbol) {
            case 'g':
                $value *= 1024;
                // no break
            case 'm':
                $value *= 1024;
                // no break
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    private function getCacheTag(string $fileId): string
    {
        return self::TAG_PREFIX . str_replace('-', '_', $fileId);
    }

    private function getCacheElementName(string $fileId, int $chunkIndex): string
    {
        return self::ITEM_PREFIX . str_replace('-', '_', $fileId) . '_' . $chunkIndex;
    }
}
