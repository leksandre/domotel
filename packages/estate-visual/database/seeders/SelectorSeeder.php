<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Estate\Models\Complex;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\StepElementAnglePointer;
use Kelnik\EstateVisual\Models\Steps\Factory;

final class SelectorSeeder extends Seeder
{
    private const SELECTOR_MAX_COUNT = 5;

    public function run(): void
    {
        $this->truncateModels();

        $complex = Complex::query()->where('active', true)->first();

        if (!$complex || !$complex->exists) {
            return;
        }

        $steps = [
            Factory::STEP_COMPLEX,
            Factory::STEP_BUILDING,
            Factory::STEP_SECTION,
            Factory::STEP_FLOOR
        ];
        $stepsCount = count($steps);

        $rows = Selector::factory()
            ->count(rand(1, self::SELECTOR_MAX_COUNT))
            ->sequence(fn ($sequence) => [
                'complex_id' => $complex->getKey(),
                'active' => true,
                'title' => $complex->title,
                'steps' => json_encode(
                    array_slice($steps, 0, rand(1, $stepsCount))
                )
            ])
            ->make()
            ->toArray();

        Selector::query()->insert($rows);

        $selectors = Selector::query()->select(['id', 'active', 'steps'])->get();
        $rows = [];

        foreach ($selectors as $selector) {
            foreach ($selector->steps as $stepName) {
                $step = Factory::make($stepName, $selector);

                $rows[] = StepElement::factory()
                    ->makeOne([
                        'selector_id' => $selector->getKey(),
                        'step' => $stepName,
                        'estate_model' => $step->getEstateModelNamespace(),
                        'title' => $step->getTitle()
                    ])
                    ->toArray();
            }
        }
        unset($step, $selector, $selectors);

        StepElement::query()->insert($rows);
    }

    public function truncateModels(): void
    {
        $models = [
            Selector::class,
            StepElement::class,
            StepElementAngle::class,
            StepElementAngleMask::class,
            StepElementAnglePointer::class
        ];

        foreach ($models as $modelNamespace) {
            $modelNamespace::query()->truncate();
        }
    }
}
