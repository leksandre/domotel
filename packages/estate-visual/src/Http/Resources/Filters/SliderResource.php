<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Filters;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property Collection $resource
 */
final class SliderResource extends JsonResource
{
    public function __construct($resource, private readonly ?Collection $curData = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        $minValue = $this->curData->get('minValue') ?? max($curData['min'] ?? 0, $this->resource->get('min'));
        $maxValue = $this->curData->get('maxValue')
            ?? min(
                $this->curData->get('max') ?? $this->resource->get('max'),
                $this->resource->get('max')
            );

        return [
            'type' => $this->resource->get('type'),
            'category' => $this->resource->get('name'),
            'label' => $this->resource->get('title'),
            'value' => [$minValue, $maxValue],
            'min' => $this->resource->get('min'),
            'max' => $this->resource->get('max')
        ];
    }
}
