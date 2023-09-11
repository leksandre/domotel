<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\Contracts;

interface HasSearchConfig
{
    public function getConfigData(): array;

    public function getPopupId(int|string $formId): string;
}
