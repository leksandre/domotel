<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\Contracts\NumericValueExtractor;

final class FloatValueExtractor extends NumericValueExtractor
{
    public const NAME = 'float';

    public function name(): string
    {
        return self::NAME;
    }

    public function __invoke(...$values): float
    {
        return (float)$this->prepareNumeric((string)current($values));
    }
}
