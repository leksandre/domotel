<?php

declare(strict_types=1);

namespace Kelnik\Core\Enums;

enum ClearingResultType: string
{
    case Queue = 'queue';
    case Sync = 'sync';

    public function isPending(): bool
    {
        return $this !== self::Sync;
    }
}
