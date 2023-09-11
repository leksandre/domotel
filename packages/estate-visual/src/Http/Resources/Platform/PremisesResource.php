<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Platform;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Services\Contracts\EstateService;

/**
 * @property Premises $resource
 */
final class PremisesResource extends JsonResource
{
    public function __construct($resource, private readonly ?EstateService $estateService = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $title = $this->resource->title . ' (' . $this->resource->external_id . ')';

        if ($this->estateService) {
            $title = $this->estateService->getInternalTypeTitle($this->resource);
        }

        return [
            'id' => $this->resource->getKey(),
            'external_id' => $this->resource->external_id,
            'serialNumber' => $this->resource->number,
            'title' => $title
        ];
    }
}
