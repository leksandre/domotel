<?php

declare(strict_types=1);

namespace Kelnik\Image\Contracts;

use Closure;

interface Picture
{
    public function __construct(ImageFile $attachment, Config $config);

    public static function init(int|string|ImageFile $imageFile): self;

    /**
     * @param int[] $breakpoints
     *
     * @return $this
     */
    public function setBreakpoints(array $breakpoints): self;

    public function setPictureAttribute(string $name, string|Closure $value): self;

    public function setSourceAttribute(string $name, string|Closure $value): self;

    public function setImageAttribute(string $name, string|Closure $value): self;

    public function setLazyLoad(bool $value): self;

    public function setLazyLoadBackgroundWidth(int $width): self;

    public function setReplaceFormats(bool $value): self;

//    public function setWatermark(bool $value): self;

    public static function getResizedPath(ImageFile $imageFile, AbstractParams $params): string;

    public function render(): ?string;
}
