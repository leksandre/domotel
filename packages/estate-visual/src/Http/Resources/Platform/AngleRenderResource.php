<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Platform;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\EstateVisual\Http\Resources\Traits\GetImageSizes;
use Orchid\Attachment\Models\Attachment;

/**
 * @property Attachment $resource
 */
final class AngleRenderResource extends JsonResource
{
    use GetImageSizes;

    public function toArray($request): array|JsonSerializable|Arrayable
    {
        [$width, $height] = $this->getImageSizes();

        return [
            'id' => $this->resource->getKey(),
            'path' => $this->resource->url,
            'width' => $width,
            'height' => $height
        ];
    }
}
