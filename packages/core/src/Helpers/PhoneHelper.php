<?php

declare(strict_types=1);

namespace Kelnik\Core\Helpers;

final class PhoneHelper
{
    public static function normalize(string $phone): string
    {
        return strlen($phone) ? preg_replace('![^0-9+]+!', '', $phone) : $phone;
    }
}
