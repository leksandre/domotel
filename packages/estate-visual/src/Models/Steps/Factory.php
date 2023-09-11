<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Steps;

use InvalidArgumentException;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\Steps\Contracts\Step;

final class Factory
{
    public const STEP_COMPLEX = 'complex';
    public const STEP_BUILDING = 'building';
    public const STEP_SECTION = 'section';
    public const STEP_FLOOR = 'floor';

    public const STEP_TO_CLASS = [
        self::STEP_COMPLEX => StepComplex::class,
        self::STEP_BUILDING => StepBuilding::class,
        self::STEP_SECTION => StepSection::class,
        self::STEP_FLOOR => StepFloor::class
    ];

    public static function make(string $stepName, Selector $selector): Step
    {
        $className = self::STEP_TO_CLASS[$stepName] ?? false;

        if (!$className) {
            throw new InvalidArgumentException('Step ' . $stepName . ' not found');
        }

        return new $className($selector);
    }
}
