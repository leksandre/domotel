<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Platform;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\EstateVisual\Models\StepElementAnglePointer;

/** @property StepElementAnglePointer $resource */
final class AnglePointerResource extends JsonResource
{
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->getKey(),
            'type' => $this->resource->type->name(),
            'data' => $this->resource->data,
            'position' => $this->resource->position->toArray()
        ];
    }
}
