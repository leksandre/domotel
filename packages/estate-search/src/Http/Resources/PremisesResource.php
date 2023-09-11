<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Params;
use Kelnik\Image\Picture;
use Orchid\Attachment\Models\Attachment;

/** @property Premises $resource */
final class PremisesResource extends JsonResource
{
    private const VIEW_TYPE_CARD = 'card';
    private const VIEW_TYPE_POPUP = 'popup';
    private const PLAN_MAX_WIDTH = 1040;

    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $options = [];

        if ($this->resource->relationLoaded('features') && $this->resource->features->isNotEmpty()) {
            /** @var PremisesFeature $el */
            foreach ($this->resource->features as $el) {
                $options[] = [
                    'id' => $el->getKey(),
                    //'icon' => $el->relationLoaded('icon') ? $el->icon->url() : null,
                    'title' => $el->full_title
                ];
            }
        }

        $priceVisible = $this->resource->price_is_visible && $this->resource->price_total;
        $hasCompletion = $this->resource->floor->building?->relationLoaded('completion')
            && $this->resource->floor->building?->completion?->title;

        $res = [
            'id' => $this->resource->getKey(),
            'type' => $this->resource->type->getKey(),
            'link' => $this->resource->url,
            'title' => $this->resource->typeShortTitle,
            'plan' => $this->getImagePlan(),
            'area' => round($this->resource->area_total, 1),
            'price' => $priceVisible ? $this->resource->price_total : null,
            'showPrice' => $priceVisible,
            'state' => [
                'available' => $this->resource->status->card_available,
                'title' => $this->resource->status->title,
                'additionalTitle' => $this->resource->status->additional_text,
                'icon' => $this->resource->status->relationLoaded('icon')
                    ? $this->resource->status->icon?->url()
                    : null
            ],
            'block' => $this->resource->floor->building?->title,
            'section' => $this->resource->relationLoaded('section') ? $this->resource->section?->title : null,
            'floor' => [
                'value' => $this->resource->floor->title,
                'total' => $this->resource->floor_max
            ],
            'delivery' => $hasCompletion
                ? [
                    'label' => trans('kelnik-estate-search::front.delivery'),
                    'title' => $this->resource->floor->building?->completion?->title
                ]
                : null,
            'viewType' => $this->getViewType(),
            'description' => $this->resource->url ? null : $this->getDescription()
        ];

        if ($options) {
            $res['options'] = $options;
        }

        if ($priceVisible && $this->resource->price_sale) {
            $res['price'] = $this->resource->price_sale;
            $res['actionPrice'] = [
                'value' => $this->resource->discount,
                'base' => $this->resource->price_total
            ];
        }

        return $res;
    }

    private function getImagePlan(): array
    {
        $list = [];

        // Берем картинку плана помещения
        //
        if ($this->resource->relationLoaded('planoplan') && $this->resource->planoplan->isAvailable()) {
            $list[] = [
                'image' => $this->resource->planoplan->widget->plan(),
                'text' => trans('kelnik-estate::front.components.premisesCard.plan.plan'),
                'stub' => false
            ];
        }

        if (!$list) {
            $images = [
                'imageList' => 'kelnik-estate::front.components.premisesCard.plan.plan',
                'imagePlan' => 'kelnik-estate::front.components.premisesCard.plan.plan',
                'image3D' => 'kelnik-estate::front.components.premisesCard.plan.plan3D'
            ];

            foreach ($images as $relationName => $text) {
                if (!$this->resource->relationLoaded($relationName) || !$this->resource->{$relationName}->exists) {
                    continue;
                }
                $list[] = [
                    'image' => $this->getImageUrl($this->resource->{$relationName}),
                    'text' => trans($text),
                    'stub' => false
                ];

                break;
            }
        }

        // Если нет плана, то выводим заглушку
        //
        if (!$list) {
            $list[] = [
                'image' => $this->resource->imagePlanDefault,
                'text' => null,
                'stub' => true
            ];
        }

        if ($this->resource->relationLoaded('imageOnFloor') && $this->resource->imageOnFloor->exists) {
            $list[] = [
                'image' => $this->getImageUrl($this->resource->imageOnFloor),
                'text' => trans('kelnik-estate::front.components.premisesCard.plan.onFloor'),
                'stub' => false
            ];
        }

        if (
            $this->resource->relationLoaded('floor')
            && $this->resource->floor->relationLoaded('building')
            && $this->resource->floor->building->relationLoaded('complexPlan')
            && $this->resource->floor->building->complexPlan->exists
        ) {
            $list[] = [
                'image' => $this->getImageUrl($this->resource->floor->building->complexPlan),
                'text' => trans('kelnik-estate::front.components.premisesCard.plan.onBuildingPlan'),
                'stub' => false
            ];
        }

        return $list;
    }

    private function getImageUrl(Attachment $image): string
    {
        if (
            !$image->exists
            || mb_strtolower($image->extension) === 'svg'
            || !resolve(CoreService::class)->hasModule('image')
        ) {
            return $image->url();
        }

        $imageFile = new ImageFile($image);
        $params = new Params($imageFile);
        $params->width = self::PLAN_MAX_WIDTH;

        return Picture::getResizedPath($imageFile, $params);
    }

    private function getViewType(): ?string
    {
        if (!$this->resource->status->card_available) {
            return null;
        }

        return $this->resource->url ? self::VIEW_TYPE_CARD : self::VIEW_TYPE_POPUP;
    }

    private function getDescription(): string
    {
        $res = [];
        $floor = $building = false;

        if ($this->resource->relationLoaded('floor')) {
            $floor = &$this->resource->floor;
            $res[] = trans('kelnik-estate-search::front.premises.description.floor') . ': ' . $floor->title;
        }

        if ($this->resource->relationLoaded('section')) {
            array_unshift(
                $res,
                trans('kelnik-estate-search::front.premises.description.section') . ': ' . $this->section->title
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

        $res[] = $this->resource->typeShortTitle . ' (' . $this->resource->getKey() . ')';

        return implode(', ', $res);
    }
}
