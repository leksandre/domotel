<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Enums;

use Kelnik\Core\Models\Enums\Contracts\HasTitle;

enum PaginationType: string implements HasTitle
{
    case Frontend = 'frontend';
    case Backend = 'backend';

    public function title(): ?string
    {
        return trans('kelnik-estate-search::admin.components.search.type.' . $this->value);
    }

    public function returnAllResults(): bool
    {
        return $this === self::Frontend;
    }

    public function usePagination(): bool
    {
        return $this === self::Backend;
    }

    public static function getDefault(): self
    {
        return self::Backend;
    }
}
