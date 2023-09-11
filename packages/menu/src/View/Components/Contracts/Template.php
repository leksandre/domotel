<?php

declare(strict_types=1);

namespace Kelnik\Menu\View\Components\Contracts;

abstract class Template
{
    public function __construct(public readonly string $name, public readonly string $title)
    {
    }
}
