<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Traits;

use Kelnik\Core\Helpers\ImageHelper;
use Throwable;

trait GetImageSizes
{
    private function getImageSizes(): array
    {
        if (!$this->resource->exists) {
            return [0, 0];
        }

        try {
            return $this->resource->getMimeType() === 'image/svg+xml'
                ? ImageHelper::getSvgSizes($this->resource)
                : ImageHelper::getImageSizes($this->resource);
        } catch (Throwable $e) {
            return [0, 0];
        }
    }
}
