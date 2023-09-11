<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;

abstract class Position implements Arrayable
{
    public function __construct(public int $left = 0, public int $top = 0)
    {
    }

    public function toArray(): array
    {
        return [$this->left, $this->top];
    }

    public function isZero(): bool
    {
        return $this->left === 0 && $this->top === 0;
    }
}
