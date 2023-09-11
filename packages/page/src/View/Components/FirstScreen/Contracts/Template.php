<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\FirstScreen\Contracts;

abstract class Template
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly string $iconPath,
        public readonly string $actionTemplate,
        public readonly string $estateTemplate,
        public array $imageBreakPoints = []
    ) {
    }
}
