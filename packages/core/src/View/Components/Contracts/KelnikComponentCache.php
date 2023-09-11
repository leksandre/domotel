<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components\Contracts;

interface KelnikComponentCache
{
    public function getCacheId(): string;
}
