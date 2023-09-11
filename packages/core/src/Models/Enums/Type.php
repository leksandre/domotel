<?php

declare(strict_types=1);

namespace Kelnik\Core\Models\Enums;

use Kelnik\Core\Models\Enums\Contracts\HasTitle;

enum Type: string implements HasTitle
{
    case Site = 'site';
    case Touch = 'touch';
    case Vr = 'vr';

    public function title(): string
    {
        return trans('kelnik-core::admin.site.types.' . $this->value);
    }

    public function isSite(): bool
    {
        return $this->value === self::Site->value;
    }

    public function isTouch(): bool
    {
        return $this->value === self::Touch->value;
    }

    public function isVr(): bool
    {
        return $this->value === self::Vr->value;
    }
}
