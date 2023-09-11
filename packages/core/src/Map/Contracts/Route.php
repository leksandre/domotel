<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Contracts;

interface Route
{
    public function __construct(array $data);

    public function getStart(): Coords;

    public function getEnd(): Coords;
}
