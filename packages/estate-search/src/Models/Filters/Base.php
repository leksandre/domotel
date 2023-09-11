<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractFilter;
use Kelnik\EstateSearch\Services\Contracts\SearchService;

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

    public function getTitle(): string
    {
        return '';
    }

    public function getAdminTitle(): string
    {
        return '';
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

        if (!empty($this->additionalValues[SearchService::PARAM_TYPES])) {
            $res->add(['type.group_id', 'in', $this->additionalValues[SearchService::PARAM_TYPES]]);
        }

        if (!empty($this->additionalValues[SearchService::PARAM_STATUSES])) {
            $res->add(['status_id', 'in', $this->additionalValues[SearchService::PARAM_STATUSES]]);
        }

        return $res;
    }
}
