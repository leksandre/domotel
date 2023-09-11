<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Filters\Contracts;

use Illuminate\Support\Collection;

abstract class AbstractSliderFilter extends AbstractFilter
{
    public function getType(): string
    {
        return static::TYPE_SLIDER;
    }

    public function getDataFilterParams(): Collection
    {
        if (in_array(static::NAME, $this->excludeParams)) {
            return new Collection();
        }

        $res = new Collection();

        $minValue = $this->getMinRequestValue();
        $maxValue = $this->getMaxRequestValue();

        if ($minValue) {
            $res->add([static::FILTER_FIELD, '>=', $minValue]);
        }

        if ($maxValue) {
            $res->add([static::FILTER_FIELD, '<=', $maxValue]);
        }

        return $res;
    }

    abstract public function getMinRequestValue(): int|float;

    abstract public function getMaxRequestValue(): int|float;
}
