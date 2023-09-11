<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractSliderFilter;

final class Price extends AbstractSliderFilter
{
    public const NAME = 'price';
    protected const FILTER_FIELD = 'price_total';
    public const ADJUSTMENT = 1_000;
    public const ADJUSTMENT_AT_MILLION = 1_000_000;
    public const CORRECTION = 10;
    protected const TITLE = 'kelnik-estate-search::front.form.price.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.price';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $data = $this->repository->getPriceValues(
            $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
        );

        $minData = (float)$data?->min_value ?? 0;
        $maxData = (float)$data?->max_value ?? 0;

        $minValue = $maxValue = null;
        foreach (['min', 'max'] as $el) {
            if (!isset($this->requestValues[self::NAME][$el])) {
                continue;
            }
            $val = (float)$this->requestValues[self::NAME][$el];

            ${$el . 'Value'} = $this->normalizeValue($val);
        }

        return new Collection([
            'type' => $this->getType(),
            'name' => self::NAME,
            'title' => $this->getTitle(),
            'min' => floor($minData / self::ADJUSTMENT) * self::ADJUSTMENT,
            'max' => ceil($maxData / self::ADJUSTMENT) * self::ADJUSTMENT,
            'minMillion' => floor($minData / self::ADJUSTMENT_AT_MILLION * self::CORRECTION) / self::CORRECTION,
            'maxMillion' => ceil($maxData / self::ADJUSTMENT_AT_MILLION * self::CORRECTION) / self::CORRECTION,
            'mean' => round((($minData + $maxData) / 2) / self::ADJUSTMENT) * self::ADJUSTMENT,
            'meanMillion' => round((($minData + $maxData) / 2) / self::ADJUSTMENT_AT_MILLION, 1),
            'minValue' => $minValue,
            'minValueMillion' => $minValue ? round($minValue / self::ADJUSTMENT_AT_MILLION, 1) : null,
            'maxValue' => $maxValue,
            'maxValueMillion' => $maxValue ? round($maxValue / self::ADJUSTMENT_AT_MILLION, 1) : null
        ]);
    }

    public function getMinRequestValue(): int|float
    {
        return $this->normalizeValue(
            (float)Arr::get($this->requestValues, self::NAME . '.min', 0)
        );
    }

    public function getMaxRequestValue(): int|float
    {
        return $this->normalizeValue(
            (float)Arr::get($this->requestValues, self::NAME . '.max', 0)
        );
    }

    private function normalizeValue(float $val): float
    {
        return $val;
    }
}
