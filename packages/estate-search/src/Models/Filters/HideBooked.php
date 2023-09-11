<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractToggleFilter;

final class HideBooked extends AbstractToggleFilter
{
    public const NAME = 'hbooked';
    protected const FILTER_FIELD = 'status_id';
    protected const FILTER_VALUE_INDEX = 2;
    protected const TITLE = 'kelnik-estate-search::front.form.hideBooked.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.hideBooked';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);
        $statusFilter = $dataFilter->first(static fn (array $filter) => $filter[0] === 'status_id');
        $hasBooked = false;

        if (
            !$statusFilter
            || empty($statusFilter[self::FILTER_VALUE_INDEX])
            || in_array($this->getStatusId(), $statusFilter[self::FILTER_VALUE_INDEX], true)
        ) {
            $dataFilter->add($this->getFilter());
            $hasBooked = $this->repository->hasPremisesByFilter($dataFilter);
        }

        $res->put(
            'values',
            $hasBooked ? ['id' => self::VALUE] : []
        );

        return $res;
    }

    public function getDataFilterParams(): Collection
    {
        $value = (int)Arr::get($this->requestValues, self::NAME, 0);
        $res = new Collection();

        if (!$value) {
            return $res;
        }

        $res->add($this->getFilter());

        return new Collection($res);
    }

    private function getFilter(): array
    {
        return [self::FILTER_FIELD, '!=', $this->getStatusId()];
    }

    private function getStatusId(): int
    {
        return PremisesStatus::BOOKED;
    }
}
