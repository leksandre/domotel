<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractSliderFilter;

final class Floor extends AbstractSliderFilter
{
    public const NAME = 'floor';
    protected const FILTER_FIELD = 'floor.number';
    protected const TITLE = 'kelnik-estate-search::front.form.floor.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.floor';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $data = $this->repository->getFloorValues(
            $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
        );

        $minValue = $maxValue = null;
        foreach (['min', 'max'] as $el) {
            if (!isset($this->requestValues[self::NAME][$el])) {
                continue;
            }
            ${$el . 'Value'} = (int)$this->requestValues[self::NAME][$el];
        }

        return new Collection([
            'type' => $this->getType(),
            'name' => self::NAME,
            'title' => $this->getTitle(),
            'min' => $data?->min_value ?? 0,
            'max' => $data?->max_value ?? 0,
            'minValue' => $minValue,
            'maxValue' => $maxValue
        ]);
    }

    public function getMinRequestValue(): int|float
    {
        return (int)Arr::get($this->requestValues, self::NAME . '.min', 0);
    }

    public function getMaxRequestValue(): int|float
    {
        return (int)Arr::get($this->requestValues, self::NAME . '.max', 0);
    }
}
