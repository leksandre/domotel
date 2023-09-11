<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;
use Kelnik\EstateSearch\Models\Enums\PaginationViewType;

/** @property Collection $resource */
final class PaginationResource extends JsonResource
{
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $pagination = $this->resource->get('pagination', []);

        return [
            'type' => Arr::get($pagination, 'type')?->value ?? PaginationViewType::getDefault()->value,
            'page' => Arr::get($pagination, 'current', 1),
            'pages' => Arr::get($pagination, 'total', 1),
            'count' => Arr::get($this->resource->get('count'), 'total', 1),
            'limit' => Arr::get($pagination, 'items', 1),
        ];
    }
}
