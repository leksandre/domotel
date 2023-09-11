<?php

declare(strict_types=1);

namespace Kelnik\Core\Map;

final class Coords implements Contracts\Coords
{
    public function __construct(public float $lat = self::DEFAULT_COORDS, public float $lng = self::DEFAULT_COORDS)
    {
    }

    public function toArray(): array
    {
        return [$this->lat, $this->lng];
    }
}
