<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\MaskTypes;

use Kelnik\EstateVisual\Http\Resources\MaskTypes\Contracts\MaskType;
use Kelnik\EstateVisual\Models\Enums\MaskType as MaskTypeEnum;

final class UrlMaskType extends MaskType
{
    protected MaskTypeEnum $type = MaskTypeEnum::Url;

    public function toArray(): array
    {
        return [
            'id' => null,
            'link' => $this->mask->value,
            'isSimpleLink' => true,
            'render' => null,
            'path' => $this->mask->coords,
            'disabled' => false,
            'isHovered' => false,
            'isShowTooltip' => false,
            'tooltip' => false,
            'pointer' => false
        ];
    }
}
