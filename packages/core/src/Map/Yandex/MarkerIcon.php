<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Yandex;

use Kelnik\Core\Map\Contracts\Icon;

final class MarkerIcon implements Icon
{
    public const WIDTH = 30;
    public const HEIGHT = 30;
    public const REAL_WIDTH = 56;
    public const REAL_HEIGHT = 56;

    public const COMPLEX_WIDTH = 48;
    public const COMPLEX_HEIGHT = 48;
    public const COMPLEX_REAL_WIDTH = 96;
    public const COMPLEX_REAL_HEIGHT = 96;

    private readonly ?string $type;
    private readonly ?string $url;
    private readonly int $width;
    private readonly int $height;

    public function __construct(string $type, string $url, int $width, int $height)
    {
        $this->type = $type;
        $this->url = $url;
        $this->width = $width ?: self::WIDTH;
        $this->height = $height ?: self::HEIGHT;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'src' => $this->url,
            'size' => [
                'width' => $this->width,
                'height' => $this->height
            ]
        ];
    }
}
