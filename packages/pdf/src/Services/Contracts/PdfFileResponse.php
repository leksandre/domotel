<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Services\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface PdfFileResponse
{
    public function __construct(string $filePath, Filesystem $storage);

    public function getContent(): ?string;

    public function download(?string $name = null): StreamedResponse;

    public function readStream();
}
