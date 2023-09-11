<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;

/**
 * @property Collection $resource
 */
final class SettingsResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var SearchConfig $config */
        $config = $this->resource->get('config');

        return [
            'popup' => $config->popup
                ? ['id' => $config->popup]
                : false,
            'hidePrice' => [
                'status' => false,
                'text' => trans('kelnik-estate-visual::front.hidePrice')
            ]
        ];
    }
}
