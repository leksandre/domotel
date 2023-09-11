<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components\Contracts;

interface KelnikComponentAlias
{
    public static function getAlias(): string;
}
