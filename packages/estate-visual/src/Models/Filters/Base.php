<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractFilter;

final class Base extends AbstractFilter
{
    public function getType(): string
    {
        return self::TYPE_BASE;
    }

    public function getName(): string
    {
        return '';
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

    public function getDataFilterParams(): Collection
    {
        $res = new Collection([
            ['active', '=', true],
            ['floor.active', '=', true],
            ['floor.building.active', '=', true],
            ['floor.building.complex.active', '=', true]
        ]);

        if (
            !empty($this->requestValues[self::PARAM_TYPES])
            && !in_array(self::PARAM_TYPES, $this->excludeParams)
        ) {
            $res->add(['type.group_id', 'in', $this->requestValues[self::PARAM_TYPES]]);
        }

        if (
            !empty($this->requestValues[self::PARAM_STATUSES])
            && !in_array(self::PARAM_STATUSES, $this->excludeParams)
        ) {
            $res->add(['status_id', 'in', $this->requestValues[self::PARAM_STATUSES]]);
        }

        return $res;
    }
}
