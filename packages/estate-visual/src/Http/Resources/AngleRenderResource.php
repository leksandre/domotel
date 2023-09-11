<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Orchid\Attachment\Models\Attachment;

final class AngleRenderResource extends JsonResource
{
    /** @var Attachment $resource */
    public $resource;

    public function __construct($resource, private readonly ?array $shift = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $shift = $this->shift ?? [0, 0];

        return [
            'link' => $this->resource->url(),
            'shift' => [
                'horizontal' => $shift[0],
                'vertical' => $shift[1],
            ]
        ];
    }
}
