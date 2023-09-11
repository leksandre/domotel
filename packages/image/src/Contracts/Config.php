<?php

declare(strict_types=1);

namespace Kelnik\Image\Contracts;

/**
 * Interface Config
 * @package Kelnik\Image\Contracts
 */
interface Config
{
    /** @return int[] */
    public function breakpoints(): array;

    public function storageDisk(): string;

    public function storagePath(): string;

    public function baseUrl(): string;

    public function driver(): string;

    public function blurAmount(): int;

    /** @return int[] */
    public function quality(): array;

    /** @return float[] */
    public function pixelRatio(): array;

    /** @return string[] */
    public function additionalFormats(): array;

    public function maxWidth(): int;
    public function maxHeight(): int;

    public function useLazyLoad(): bool;
    public function lazyLoadBackgroundWidth(): int;

    /** @return string[] */
    public function replaceFormats(): array;

    public function useOriginalPath(): bool;
}
