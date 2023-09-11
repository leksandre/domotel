<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Coords extends Arrayable
{
    public const DEFAULT_COORDS = 0;

    public function __construct(float $lat, float $lng);
}
