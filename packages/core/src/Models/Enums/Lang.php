<?php

declare(strict_types=1);

namespace Kelnik\Core\Models\Enums;

use Kelnik\Core\Models\Enums\Contracts\HasTitle;

enum Lang: string implements HasTitle
{
    case Russian = 'ru';
    case English = 'en';
    case Deutsch = 'de';

    public function title(): string
    {
        return trans('kelnik-core::langs.' . $this->value .  '.title');
    }
}
