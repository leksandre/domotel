<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\Contracts\ValueExtractor;

final class StringValueExtractor implements ValueExtractor
{
    public const NAME = 'string';

    public function name(): string
    {
        return self::NAME;
    }

    public function __invoke(...$values): string
    {
        return trim((string)current($values));
    }
}
