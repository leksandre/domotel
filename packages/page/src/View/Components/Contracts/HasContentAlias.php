<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Contracts;

interface HasContentAlias
{
    public function getContentAlias(): ?string;
}
