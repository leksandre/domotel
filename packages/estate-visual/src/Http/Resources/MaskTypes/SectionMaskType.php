<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\MaskTypes;

use Kelnik\EstateVisual\Http\Resources\MaskTypes\Contracts\MaskType;
use Kelnik\EstateVisual\Models\Enums\MaskType as MaskTypeEnum;

final class SectionMaskType extends MaskType
{
    protected MaskTypeEnum $type = MaskTypeEnum::Section;
    protected bool $usePremisesStat = true;
}
