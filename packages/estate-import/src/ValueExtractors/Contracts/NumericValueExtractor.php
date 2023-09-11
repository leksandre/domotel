<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\ValueExtractors\Contracts;

abstract class NumericValueExtractor implements ValueExtractor
{
    protected function prepareNumeric(string $value): string
    {
        return preg_replace(['![^\d.,\-]!i', '!,!', '!-{2,}!'], ['', '.', '-'], $value);
    }
}
