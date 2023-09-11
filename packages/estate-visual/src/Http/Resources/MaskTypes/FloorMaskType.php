<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\MaskTypes;

use Kelnik\Core\Helpers\NumberHelper;
use Kelnik\EstateVisual\Http\Resources\MaskTypes\Contracts\MaskType;
use Kelnik\EstateVisual\Models\Contracts\Position;
use Kelnik\EstateVisual\Models\Enums\MaskType as MaskTypeEnum;

final class FloorMaskType extends MaskType
{
    protected MaskTypeEnum $type = MaskTypeEnum::Floor;
    protected bool $usePremisesStat = true;

    public function getPointer(Position $pointer): false|array
    {
        $res = parent::getPointer($pointer);

        if (!$res || empty($res['text'])) {
            return $res;
        }

        if (!is_numeric($res['text'])) {
            $res['text'] = NumberHelper::prepareNumeric($res['text']);
        }

        return $res;
    }
}
