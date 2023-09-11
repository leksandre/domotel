<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Enums;

enum RedirectType: int
{
    case Disabled = 0;
    case MovedPermanently = 301;
    case MovedTemporary = 302;
//    case SeeOther = 303;
//    case TemporaryRedirect = 307;
//    case PermanentlyRedirect = 308;

    public function title(): string
    {
        return trans('kelnik-page::admin.redirectTypes.' . $this->value) ?? '';
    }

    public function canBeRedirected(): bool
    {
        return $this !== self::Disabled;
    }
}
