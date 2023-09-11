<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters\Contracts;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class AbstractToggleFilter extends AbstractFilter
{
    protected const VALUE = 1;

    public function getType(): string
    {
        return static::TYPE_TOGGLE;
    }

    public function getResult(Collection $dataFilter): ?Collection
    {
        $selected = (int)Arr::get($this->requestValues, static::NAME, 0);

        return new Collection([
            'type' => $this->getType(),
            'name' => static::NAME,
            'title' => $this->getTitle(),
            'values' => [],
            'selected' => $selected
        ]);
    }

    public function getDataFilterParams(): Collection
    {
        $values = Arr::get($this->requestValues, static::NAME, []);
        $values = array_map('intval', $values);
        $values = array_filter($values);

        if (!$values) {
            return new Collection();
        }

        return new Collection([
            [static::FILTER_FIELD, '=', $values]
        ]);
    }
}
