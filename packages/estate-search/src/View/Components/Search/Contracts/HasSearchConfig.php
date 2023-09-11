<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\View\Components\Search\Contracts;

interface HasSearchConfig
{
    public function getConfigData(): array;

    public function getPopupId(): string;
}
