<?php

declare(strict_types=1);

namespace Kelnik\Image\Contracts;

interface ImageFile
{
    public function getName(): string;

    public function getFullName(): string;

    public function getOriginalName(): string;

    public function getExtension(): string;

    public function getMimeType(): string;

    public function getPath(): string;

    public function getUrl(): string;

    public function getWidth(): int;

    public function getHeight(): int;

    /**
     * Size in bytes
     * @return int
     */
    public function getSize(): int;
}
