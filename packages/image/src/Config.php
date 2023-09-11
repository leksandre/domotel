<?php

declare(strict_types=1);

namespace Kelnik\Image;

final class Config implements Contracts\Config
{
    public function baseUrl(): string
    {
        return config('kelnik-image.route.prefix');
    }

    public function storageDisk(): string
    {
        return config('kelnik-image.disk', config('filesystems.default'));
    }

    public function storagePath(): string
    {
        return config('kelnik-image.path', 'image');
    }

    public function breakpoints(): array
    {
        return config('kelnik-image.breakpoints', []);
    }

    public function driver(): string
    {
        return config('kelnik-image.driver');
    }

    public function blurAmount(): int
    {
        return config('kelnik-image.blurAmount');
    }

    public function quality(): array
    {
        return config('kelnik-image.quality');
    }

    public function pixelRatio(): array
    {
        return config('kelnik-image.pixelRatio', []);
    }

    public function additionalFormats(): array
    {
        return $this->arrayKeyAndValueToLower(
            config('kelnik-image.additionalFormats', [])
        );
    }

    public function maxWidth(): int
    {
        return (int)config('kelnik-image.maxWidth', 0);
    }

    public function maxHeight(): int
    {
        return (int)config('kelnik-image.maxHeight', 0);
    }

    public function useLazyLoad(): bool
    {
        return config('kelnik-image.lazyLoad') === true;
    }

    public function lazyLoadBackgroundWidth(): int
    {
        return (int)config('kelnik-image.lazyLoadBackgroundWidth');
    }

    public function replaceFormats(): array
    {
        return $this->arrayKeyAndValueToLower(
            config('kelnik-image.replaceFormats', [])
        );
    }

    public function useOriginalPath(): bool
    {
        return config('kelnik-image.allowOriginPath') === true;
    }

    private function arrayKeyAndValueToLower(array $arr): array
    {
        if (!$arr) {
            return [];
        }

        $res = [];

        foreach ($arr as $k => $v) {
            $res[strtolower($k)] = strtolower($v);
        }
        unset($arr);

        return $res;
    }
}
