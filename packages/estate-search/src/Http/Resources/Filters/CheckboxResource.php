<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Http\Resources\Filters;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractCheckboxFilter;

/** @property Collection $resource */
final class CheckboxResource extends JsonResource
{
    public function __construct($resource, private readonly ?Collection $curData = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): ?array
    {
        $values = $this->resource->get('values');
        $res = [
            'type' => $this->resource->get('type'),
            'category' => $this->resource->get('name'),
            'view' => $this->resource->get('view') ?? AbstractCheckboxFilter::VIEW_TYPE_BUTTON,
            'label' => $this->resource->get('title')
        ];

        foreach ($values as $val) {
            $disabled = $this->curData->isNotEmpty() && !isset($this->curData->get('values')[$val['id']]);

            $res['items'][$val['id']] = [
                'id' => $val['id'],
                'value' => $val['id'],
                'name' => $val['title'],
                'title' => $val['short_title'] ?? $val['title'],
                'checked' => !$disabled
                    && $this->curData->isNotEmpty()
                    && in_array($val['id'], $this->curData->get('selected') ?? []),
                'disabled' => $disabled
            ];
        }

        return isset($res['items'])
            ? $res
            : null;
    }
}
