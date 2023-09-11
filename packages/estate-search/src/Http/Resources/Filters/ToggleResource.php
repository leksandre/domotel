<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Http\Resources\Filters;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/** @property Collection $resource */
final class ToggleResource extends JsonResource
{
    public function __construct($resource, private readonly ?Collection $curData = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): ?array
    {
        $value = $this->resource->get('values') ?? [];
        $value = Arr::get($value, 'id');

        return $value
            ? [
                'type' => $this->resource->get('type'),
                'category' => $this->resource->get('name'),
                'view' => 'toggle',
                'id' => $value,
                'title' => $this->resource->get('title'),
                'checked' => $this->curData->isNotEmpty() && ($this->curData->get('selected') ?? null),
                'disabled' => $this->curData->isNotEmpty() && ($this->curData->get('values')['id'] ?? null) !== $value
            ]
            : null;
    }
}
