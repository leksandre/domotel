<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\Contracts\ValueExtractor;

final class ArrayValueExtractor implements ValueExtractor
{
    public const NAME = 'array';

    public function name(): string
    {
        return self::NAME;
    }

    public function __invoke(...$values): array
    {
        $value = array_shift($values);
        $callback = array_shift($values);

        $value = (array)$value;

        return $value && is_callable($callback)
            ? array_map($callback, $value)
            : $value;
    }
}
