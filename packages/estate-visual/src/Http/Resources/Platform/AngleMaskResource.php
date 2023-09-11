<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Platform;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\EstateVisual\Models\StepElementAngleMask;

/**
 * @property StepElementAngleMask $resource
 */
final class AngleMaskResource extends JsonResource
{
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->getKey(),
            'type' => $this->resource->type,
            'element_id' => $this->resource->type?->isPremises()
                ? $this->resource->estate_premises_id
                : $this->resource->element_id,
            'value' => $this->resource->value,
            'pointer' => $this->resource->pointer->toArray() ?? [0, 0],
            'coords' => $this->resource->coords
        ];
    }
}
