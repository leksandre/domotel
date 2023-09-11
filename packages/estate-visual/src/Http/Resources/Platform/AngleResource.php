<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Platform;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\EstateVisual\Models\StepElementAngle;

/**
 * @property StepElementAngle $resource
 */
final class AngleResource extends JsonResource
{
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $arr = [
            'id' => $this->resource->getKey() ?? 'tmp-' . ((string)now()->getTimestamp()),
            'title' => $this->resource->title,
            'degree' => $this->resource->degree,
            'shift' => $this->resource->shift,
            'render' => AngleRenderResource::make($this->resource->render),
            'masks' => [],
            'pointers' => []
        ];

        if ($this->resource->relationLoaded('masks')) {
            $arr['masks'] = AngleMaskResource::collection($this->resource->masks);
        }

        if ($this->resource->relationLoaded('pointers')) {
            $arr['pointers'] = AnglePointerResource::collection($this->resource->pointers);
        }

        return $arr;
    }
}
