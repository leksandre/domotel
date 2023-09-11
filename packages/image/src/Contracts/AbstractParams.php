<?php

declare(strict_types=1);

namespace Kelnik\Image\Contracts;

use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractParams implements Arrayable
{
    public ?int $width = null;
    public ?int $height = null;
    public bool $crop = false;
    public bool $blur = false;
    public ?string $filename = null;

    public static function createFromArray(array $params): static
    {
        $obj = new static();
        $properties = get_class_vars(static::class);

        foreach ($params as $k => $v) {
            if (!array_key_exists($k, $properties)) {
                continue;
            }

            $obj->{$k} = $v;
        }

        return $obj;
    }

    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'crop' => $this->crop,
            'blur' => $this->blur,
            'filename' => $this->filename
        ];
    }
}
