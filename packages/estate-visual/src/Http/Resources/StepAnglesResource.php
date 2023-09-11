<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\EstateVisual\Models\StepElementAngle;

/**
 * @property StepElementAngle $resource
 */
final class StepAnglesResource extends JsonResource
{
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->getKey() ?? 0,
            'active' => $this->resource->active ?? false
        ];
    }
}
