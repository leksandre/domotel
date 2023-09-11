<?php

declare(strict_types=1);

namespace Kelnik\Menu\Models\Enums;

enum Type: int
{
    case Tree = 1;
    case Strict = 2;

    public function title(): string
    {
        return trans('kelnik-menu::admin.menuTypes.' . $this->name);
    }

    public function isStrict(): bool
    {
        return $this === self::Strict;
    }
}
