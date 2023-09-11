<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\MaskTypes;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Kelnik\EstateVisual\Http\Resources\MaskTypes\Contracts\MaskType;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\Enums\MaskType as MaskTypeEnum;

final class MaskTypeFactory
{
    private static function maskTypeToClass(): array
    {
        return [
            MaskTypeEnum::Complex->value => ComplexMaskType::class,
            MaskTypeEnum::Building->value => BuildingMaskType::class,
            MaskTypeEnum::Section->value => SectionMaskType::class,
            MaskTypeEnum::Floor->value => FloorMaskType::class,
            MaskTypeEnum::Premises->value => PremisesMaskType::class,
            MaskTypeEnum::Url->value => UrlMaskType::class
        ];
    }

    public static function make(
        StepElementAngleMask $mask,
        Collection $settings,
        ?SearchConfig $config,
        Collection $renders
    ): MaskType {
        $className = self::maskTypeToClass()[$mask->type->value] ?? false;

        if (!$className) {
            throw new InvalidArgumentException('Mask type ' . $mask->type->value . ' not found');
        }

        return new $className($mask, $settings, $config, $renders);
    }
}
