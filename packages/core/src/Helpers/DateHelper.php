<?php

declare(strict_types=1);

namespace Kelnik\Core\Helpers;

use DateTimeInterface;

final class DateHelper
{
    public static function quarter(DateTimeInterface $dateTime): int
    {
        return (int)ceil($dateTime->format('n') / 3);
    }
}
