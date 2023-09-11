<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\Contracts\NumericValueExtractor;

final class IntValueExtractor extends NumericValueExtractor
{
    public const NAME = 'int';

    public function name(): string
    {
        return self::NAME;
    }

    public function __invoke(...$values): int
    {
        return (int)$this->prepareNumeric((string)current($values));
    }
}
