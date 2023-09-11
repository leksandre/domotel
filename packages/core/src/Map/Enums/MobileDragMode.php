<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Enums;

enum MobileDragMode: string
{
    case Single = 'single';
    case Double = 'double';

    public function title(): string
    {
        return trans('kelnik-core::admin.settings.map.dragMode.types.' . $this->value);
    }

    public function isDefault(): bool
    {
        return $this === self::Double;
    }
}
