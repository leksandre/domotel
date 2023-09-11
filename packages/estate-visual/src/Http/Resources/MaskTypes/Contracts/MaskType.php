<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\MaskTypes\Contracts;

use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\Contracts\Position;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\Enums\MaskType as MaskTypeEnum;

abstract class MaskType
{
    protected MaskTypeEnum $type;
    protected bool $usePremisesStat = false;

    public function __construct(
        protected readonly StepElementAngleMask $mask,
        protected readonly Collection $settings,
        protected readonly ?SearchConfig $config,
        protected readonly Collection $renders
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->mask->element->getKey(),
            'link' => $this->getLink(),
            'render' => $this->getRender(),
            'path' => $this->mask->coords,
            'disabled' => !$this->usePremisesStat || !$this->mask->premisesStat,
            'isHovered' => false,
            'tooltip' => $this->getTooltip(),
            'pointer' => $this->getPointer($this->mask->pointer)
        ];
    }

    public function getLink(): ?string
    {
        return $this->type->value . '/' . $this->mask->element->getKey() . '/';
    }

    public function getRender(): ?string
    {
        return $this->renders->get($this->mask->element_id)?->url();
    }

    public function getTooltip(): array
    {
        $title = $this->mask->element->title;

        return [
            'title' => $title,
            'linkTitle' => trans('kelnik-estate-visual::front.goto.' . $this->type->value, ['title' => $title]),
            'params' => $this->getParams()
        ];
    }

    public function getPointer(Position $pointer): false|array
    {
        if ($pointer->isZero()) {
            return false;
        }

        return [
            'text' => $this->mask->element->title,
            'top' => $pointer->top,
            'left' => $pointer->left,
            'type' => $this->type->value
        ];
    }

    public function getParams(): array
    {
        if (!$this->mask->premisesStat) {
            return [];
        }

        return array_values(
            array_map(
                static function ($el) {
                    return [
                        'title' => $el['title'],
                        'price' => is_numeric($el['price_min']) ? (float)$el['price_min'] : $el['price_min'],
                        'amount' => $el['cnt']
                    ];
                },
                $this->mask->premisesStat
            )
        );
    }
}
