<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\Contracts\NumericValueExtractor;

final class BoolValueExtractor extends NumericValueExtractor
{
    public const NAME = 'bool';

    public function name(): string
    {
        return self::NAME;
    }

    public function __invoke(...$values): bool
    {
        $value = current($values);

        if (is_numeric($value)) {
            return (int)$value > 0;
        }

        return in_array(
            mb_strtolower(trim((string)$value)),
            ['y', 'yes', 'да', 'ja', '+'],
            true
        );
    }
}
