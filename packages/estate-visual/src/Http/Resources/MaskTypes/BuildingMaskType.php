<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\MaskTypes;

use Kelnik\Core\Helpers\NumberHelper;
use Kelnik\EstateVisual\Http\Resources\MaskTypes\Contracts\MaskType;
use Kelnik\EstateVisual\Models\Enums\MaskType as MaskTypeEnum;

final class BuildingMaskType extends MaskType
{
    protected MaskTypeEnum $type = MaskTypeEnum::Building;
    protected bool $usePremisesStat = true;

    public function getTooltip(): array
    {
        $res = parent::getTooltip();
        $completion = &$this->mask->completion;

        if ($completion && $completion->exists) {
            $res['deadline'] = $completion->title
                ? trans('kelnik-estate-visual::front.deadlineTitle', ['title' => $completion->title])
                : trans(
                    'kelnik-estate-visual::front.deadline',
                    [
                        'quarter' => NumberHelper::arabicToRoman($completion->event_date?->quarter),
                        'year' => $completion->event_date?->year
                    ]
                );
        }

        return $res;
    }
}
