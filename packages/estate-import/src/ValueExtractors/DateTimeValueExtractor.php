<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\ValueExtractors;

use DateTime;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Kelnik\EstateImport\ValueExtractors\Contracts\ValueExtractor;

final class DateTimeValueExtractor implements ValueExtractor
{
    public const NAME = 'datetime';
    public const FORMAT_DEFAULT = 'Y-m-d H:i:s';

    public function name(): string
    {
        return self::NAME;
    }

    public function __invoke(...$values): ?DateTimeInterface
    {
        $value = array_shift($values);
        $format = array_shift($values);

        if (is_string($value) && !mb_strlen($value)) {
            return null;
        }

        return $value instanceof DateTime
            ? Carbon::createFromInterface($value)
            : Carbon::createFromFormat($format ?? self::FORMAT_DEFAULT, (string)$value);
    }
}
