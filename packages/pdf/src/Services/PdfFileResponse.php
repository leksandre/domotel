<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class PdfFileResponse implements Contracts\PdfFileResponse
{
    public function __construct(private string $filePath, private Filesystem $storage)
    {
    }

    public function getContent(): ?string
    {
        return $this->storage->get($this->filePath);
    }

    public function download(?string $name = null): StreamedResponse
    {
        return $this->storage->download($this->filePath, $name);
    }

    public function readStream()
    {
        return $this->storage->readStream($this->filePath);
    }
}
