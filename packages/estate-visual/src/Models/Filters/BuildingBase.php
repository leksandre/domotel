<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractCheckboxFilter;

final class BuildingBase extends AbstractCheckboxFilter
{
    public const NAME = 'building';
    protected const FILTER_FIELD = 'floor.building_id';

    public function getType(): string
    {
        return self::TYPE_BASE;
    }

    public function getTitle(): ?string
    {
        return null;
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
