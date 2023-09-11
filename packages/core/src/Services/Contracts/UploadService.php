<?php

declare(strict_types=1);

namespace Kelnik\Core\Services\Contracts;

use Illuminate\Http\UploadedFile;
use Orchid\Attachment\Models\Attachment;

interface UploadService
{
    public function saveChunk(string $fileId, int $chunkIndex, UploadedFile $uploadedFile): bool;

    public function compileFileFromChunks(string $fileId, int $chunks, int $totalSize): ?UploadedFile;

    public function createAttachment(UploadedFile $file, string $storage, ?string $group): Attachment;

    public function getMaxUploadSize(): int;
}
