<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Icon extends Arrayable
{
    public function __construct(string $type, string $url, int $width, int $height);
}
