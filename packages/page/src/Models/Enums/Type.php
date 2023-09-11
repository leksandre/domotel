<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Enums;

enum Type: int
{
    case Simple = 0;
    case Error = 1;

    public function isSimple(): bool
    {
        return $this === self::Simple;
    }
}
