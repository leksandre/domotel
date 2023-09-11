<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Enums;

use Kelnik\Core\Models\Enums\Contracts\HasTitle;

enum PaginationViewType: string implements HasTitle
{
    case Both = 'both';
    case Page = 'page';
    case Next = 'next';

    public function title(): ?string
    {
        return trans('kelnik-estate-search::admin.components.search.viewType.' . $this->value);
    }

    public static function getDefault(): self
    {
        return self::Both;
    }
}
