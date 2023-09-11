<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Header\Contracts;

use Kelnik\Page\View\Components\Header\Enums\TemplateType;

abstract class Template
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly TemplateType $type = TemplateType::Desktop
    ) {
    }
}
