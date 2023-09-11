<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\ValueExtractors\Contracts;

interface ValueExtractor
{
    public function name(): string;

    public function __invoke(...$values): mixed;
}
