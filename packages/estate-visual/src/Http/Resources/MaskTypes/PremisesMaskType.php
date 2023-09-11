<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\MaskTypes;

use Illuminate\Support\Arr;
use Kelnik\EstateVisual\Http\Resources\MaskTypes\Contracts\MaskType;
use Kelnik\EstateVisual\Models\Enums\MaskType as MaskTypeEnum;

final class PremisesMaskType extends MaskType
{
    protected const DEFAULT_COLOR = '#95d0a1';
    protected const VIEW_TYPE_CARD = 'card';
    protected const VIEW_TYPE_POPUP = 'popup';

    protected MaskTypeEnum $type = MaskTypeEnum::Premises;

    public function toArray(): array
    {
        return [
            'id' => $this->mask->getKey(),
            'link' => $this->getLink(),
            'path' => $this->mask->coords,
            'render' => $this->getRender(),
            'disabled' => !$this->mask->premises->status->card_available,
            'maskColor' => $this->getColorByTypeAndState(
                $this->mask->premises->type->getKey(),
                $this->mask->premises->status->getKey()
            ),
            'tooltip' => $this->getTooltip(),
            'pointer' => $this->getPointer($this->mask->pointer),
            'viewType' => $this->getViewType(),
            'description' => $this->mask->premises->url ? null : $this->getDescription()
        ];
    }

    public function getLink(): ?string
    {
        return $this->mask->premises->status->card_available
            ? $this->mask->premises->url
            : null;
    }

    public function getRender(): ?string
    {
        $flat = &$this->mask->premises;

        if ($flat->relationLoaded('planoplan') && $flat->planoplan->isAvailable()) {
            return $flat->planoplan->widget->plan();
        }

        return $flat->relationLoaded('imageList') && $flat->imageList->exists
            ? $flat->imageList->url()
            : ($flat->relationLoaded('imagePlan') && $flat->imagePlan->exists
                ? $flat->imagePlan->url()
                : $flat->imagePlanDefault
            );
    }

    public function getTooltip(): array
    {
        $flat = &$this->mask->premises;

        $res = [
            'title' => $flat->typeShortTitle,
            'linkTitle' => $this->getLinkTitle(),
            'area' => $flat->area_total,
            'price' => $flat->price_is_visible ? $flat->price_total : null,
            'showPrice' => $flat->price_is_visible,
            'state' => [
                'available' => $flat->status->card_available,
                'title' => $flat->status->title,
                'additionalTitle' => $flat->status->additional_text,
                'icon' => $flat->status->relationLoaded('icon') ? $flat->status->icon?->url() : null
            ],
            'floor' => [
                'value' => $flat->floor->title,
                'total' => $flat->floor_max
            ],
            'block' => $flat->floor->building->title,
            'options' => $flat->features->pluck('icon')->pluck('url')->filter()
        ];

        if ($flat->price_is_visible && $flat->price_sale) {
            $res['price'] = $flat->price_sale;
            $res['actionPrice'] = [
                'value' => $flat->discount,
                'base' => $flat->price
            ];
        }

        return $res;
    }

    private function getColorByTypeAndState(int|string|null $type, int|string|null $state): string
    {
        return Arr::get(
            $this->settings->get('colors'),
            $type . '.' . $state,
            self::DEFAULT_COLOR
        );
    }

    private function getViewType(): ?string
    {
        if (!$this->mask->premises->status->card_available) {
            return null;
        }

        if ($this->config->popup && !$this->mask->premises->url) {
            return self::VIEW_TYPE_POPUP;
        }

        return $this->mask->premises->url
            ? self::VIEW_TYPE_CARD
            : null;
    }

    private function getDescription(): string
    {
        $res = [];
        $floor = $building = false;
        $flat = &$this->mask->premises;

        if ($flat->relationLoaded('floor')) {
            $floor = &$flat->floor;
            $res[] = trans('kelnik-estate-search::front.premises.description.floor') . ': ' . $floor->title;
        }

        if ($flat->relationLoaded('section')) {
            array_unshift(
                $res,
                trans('kelnik-estate-search::front.premises.description.section') . ': ' . $flat->section->title
            );
        }

        if ($floor && $floor->relationLoaded('building')) {
            $building = $floor->building;
            array_unshift(
                $res,
                trans('kelnik-estate-search::front.premises.description.building') . ': ' . $building->title
            );
        }

        if ($building && $building->relationLoaded('complex')) {
            array_unshift(
                $res,
                trans('kelnik-estate-search::front.premises.description.complex') .
                ': ' . $building->complex->title
            );
        }

        $res[] = $flat->typeShortTitle . ' (' . $flat->external_id . ')';

        return implode(', ', $res);
    }

    private function getLinkTitle(): bool|string
    {
        $flat = &$this->mask->premises;

        if ($this->config->popup && !$this->getLink() && !empty($this->config->form['text'])) {
            return $this->config->form['text'];
        }

        return $flat->status->card_available
            ? trans('kelnik-estate-visual::front.goto.premises')
            : false;
    }
}
