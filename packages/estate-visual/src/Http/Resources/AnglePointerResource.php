<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Kelnik\EstateVisual\Models\StepElementAnglePointer;

/** @property StepElementAnglePointer $resource */
final class AnglePointerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => $this->resource->type->name(),
            'data' => $this->resource->data,
            'top' => $this->resource->position->top,
            'left' => $this->resource->position->left
        ];
    }
}
