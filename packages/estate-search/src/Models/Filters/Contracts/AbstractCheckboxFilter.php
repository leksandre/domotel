<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters\Contracts;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class AbstractCheckboxFilter extends AbstractFilter
{
    public const VIEW_TYPE_BUTTON = 'button';
    public const VIEW_TYPE_BLOCK = 'block';

    public function getType(): string
    {
        return static::TYPE_CHECKBOX;
    }

    public function getViewType(): string
    {
        return static::VIEW_TYPE_BUTTON;
    }

    public function getResult(Collection $dataFilter): ?Collection
    {
        $selected = array_values(
            Arr::get($this->requestValues, static::NAME, [])
        );

        if ($selected) {
            $selected = array_map('intval', $selected);
            $selected = array_filter($selected);
        }

        return new Collection([
            'type' => $this->getType(),
            'name' => static::NAME,
            'title' => $this->getTitle(),
            'values' => [],
            'selected' => $selected,
            'view' => $this->getViewType()
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
            [static::FILTER_FIELD, 'in', $values]
        ]);
    }
}
