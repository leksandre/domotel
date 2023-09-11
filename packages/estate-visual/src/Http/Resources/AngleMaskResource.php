<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Http\Resources\MaskTypes\MaskTypeFactory;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;
use Kelnik\EstateVisual\Models\StepElementAngleMask;

/** @property StepElementAngleMask $resource */
final class AngleMaskResource extends JsonResource
{
    public function __construct(
        StepElementAngleMask $resource,
        private readonly Collection $settings,
        private readonly ?SearchConfig $config,
        private readonly Collection $elementsRender
    ) {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return MaskTypeFactory::make($this->resource, $this->settings, $this->config, $this->elementsRender)->toArray();
    }
}
