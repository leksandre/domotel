<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractCheckboxFilter;

final class FloorBase extends AbstractCheckboxFilter
{
    public const NAME = 'floor';
    protected const FILTER_FIELD = 'floor_id';

    public function getType(): string
    {
        return self::TYPE_BASE;
    }

    public function getTitle(): ?string
    {
        return trans('kelnik-estate-visual::front.form.floor.title');
    }

    public function getResult(Collection $dataFilter): ?Collection
    {
        return null;
    }

    public function isHidden(): bool
    {
        return true;
    }
}
