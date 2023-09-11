<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractSliderFilter;

final class Area extends AbstractSliderFilter
{
    public const NAME = 'area';
    protected const FILTER_FIELD = 'area_total';
    public const CORRECTION = 1;
    protected const TITLE = 'kelnik-estate-search::front.form.area.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.area';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $data = $this->repository->getAreaValues(
            $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
        );

        $minData = (float)($data?->min_value ?? 0);
        $maxData = (float)($data?->max_value ?? 0);

        $minValue = $maxValue = null;
        foreach (['min', 'max'] as $el) {
            if (!isset($this->requestValues[self::NAME][$el])) {
                continue;
            }
            ${$el . 'Value'} = (float)$this->requestValues[self::NAME][$el];
        }

        return new Collection([
            'type' => $this->getType(),
            'name' => self::NAME,
            'title' => $this->getTitle(),
            'min' => floor(($minData ?? 0) * self::CORRECTION) / self::CORRECTION,
            'max' => ceil(($maxData ?? 0) * self::CORRECTION) / self::CORRECTION,
            'mean' => round(($minData + $maxData) / 2),
            'minValue' => $minValue,
            'maxValue' => $maxValue
        ]);
    }

    public function getMinRequestValue(): int|float
    {
        $val = (int)Arr::get($this->requestValues, self::NAME . '.min', 0);

        if ($val) {
            $val -= 0.03;
        }

        return $val;
    }

    public function getMaxRequestValue(): int|float
    {
        $val = (int)Arr::get($this->requestValues, self::NAME . '.max', 0);

        if ($val) {
            $val += 0.03;
        }

        return $val;
    }
}
