<?php

declare(strict_types=1);

namespace Kelnik\Progress\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\Progress\Models\Camera;

final class CameraPopupResource extends JsonResource
{
    /** @var Camera */
    public $resource;

    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->getKey(),
            'title' => $this->resource->title,
//            'description' => $this->resource->description,
            'video' => [
//                'thumb' => $this->resource->cover->url(),
                'url' => $this->resource->url
            ]
        ];
    }
}
