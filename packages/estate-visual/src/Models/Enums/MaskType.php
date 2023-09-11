<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Enums;

enum MaskType: string
{
    case Complex = 'complex';
    case Building = 'building';
    case Section = 'section';
    case Floor = 'floor';
    case Premises = 'premises';
    case Url = 'url';
    case Empty = '';

    public function isPremises(): bool
    {
        return $this === self::Premises;
    }

    public function isUrl(): bool
    {
        return $this === self::Url;
    }

    public function isStep(): bool
    {
        return !in_array($this, [self::Premises, self::Url]);
    }

    public function isEmpty(): bool
    {
        return $this === self::Empty;
    }
}
