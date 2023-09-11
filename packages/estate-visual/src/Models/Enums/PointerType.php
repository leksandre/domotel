<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Enums;

use Kelnik\Core\Models\Enums\Contracts\HasTitle;

enum PointerType: int implements HasTitle
{
    case Text = 1;
    case Panorama = 2;
//    case Icon = 3;

    public function title(): ?string
    {
        return trans('kelnik-estate-visual::front.pointer.type.' . $this->name());
    }

    public function name(): string
    {
        return strtolower($this->name);
    }

    public function isPanorama(): bool
    {
        return $this === self::Panorama;
    }

    public static function tryFromName(string $value): ?self
    {
        foreach (self::cases() as $variant) {
            if ($variant->name() === $value) {
                return $variant;
            }
        }

        return null;
    }
}
