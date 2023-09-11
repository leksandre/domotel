<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractCheckboxFilter;

final class ComplexBase extends AbstractCheckboxFilter
{
    public const NAME = 'complex';
    protected const FILTER_FIELD = 'floor.building.complex_id';

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
