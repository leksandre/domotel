<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Http\Resources\Filters;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Contracts\SearchConfig;
use Kelnik\EstateSearch\Models\Orders\Contracts\AbstractOrder;

/** @property Collection $resource */
final class SettingsResource extends JsonResource
{
    public function toArray($request): array
    {
        $sort = [
            'default' => [
                'type' => '',
                'order' => AbstractOrder::DIRECTION_ASC
            ],
            'items' => []
        ];

        /** @var AbstractOrder $order */
        foreach ($this->resource->get('sortOrder') as $order) {
            if ($order->isSelected()) {
                $sort['default']['type'] = $order->getName();
                $sort['default']['order'] = $order->isSelectedWithDirection($order::DIRECTION_ASC)
                    ? $order::DIRECTION_ASC
                    : $order::DIRECTION_DESC;
            }
            $sort['items'][$order->getName()] = [
                'asc' => $order->getTitle($order::DIRECTION_ASC),
                'desc' => $order->getTitle($order::DIRECTION_DESC)
            ];
        }

        /** @var SearchConfig $config */
        $config = $this->resource->get('config');

        return [
            'view' => [
                'type' => $config->view,
                'switch' => $config->switch
            ],
            'popup' => $config->popup
                ? ['id' => $config->popup]
                : false,
            'premisesToShow' => $config->pagination->perPage,
            'groupOptions' => 2,
            'hidePrice' => [
                'status' => $this->resource->get('hidePrice'),
                'text' => trans('kelnik-estate-search::front.hidePrice')
            ],
            'sort' => $sort
        ];
    }
}
