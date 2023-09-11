<?php

declare(strict_types=1);

namespace Kelnik\FBlock\View\Components\Contracts;

abstract class Template
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly array $imageBreakPoints = []
    ) {
    }
}
