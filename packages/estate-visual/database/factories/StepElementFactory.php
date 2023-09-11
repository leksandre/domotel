<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\EstateVisual\Models\StepElement;

final class StepElementFactory extends Factory
{
    protected $model = StepElement::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->firstName()
        ];
    }
}
