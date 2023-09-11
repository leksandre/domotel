<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Platform;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\EstateVisual\Models\StepElement;

/**
 * @property StepElement $resource
 */
final class ElementResource extends JsonResource
{
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $el = &$this->resource;

        return [
            'id' => $el->getKey(),
            'title' => is_numeric($el->title) || (is_string($el->title) && mb_strlen($el->title) < 2)
                ? trans('kelnik-estate-visual::steps.' . $el->step . '.title') . ': ' . $el->title
                : $el->title
        ];
    }
}
