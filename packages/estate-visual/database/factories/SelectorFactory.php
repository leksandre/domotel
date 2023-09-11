<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\EstateVisual\Models\Selector;

final class SelectorFactory extends Factory
{
    protected $model = Selector::class;

    public function definition(): array
    {
        return [
            'active' => $this->faker->boolean,
            'external_id' => $this->faker->uuid,
            'title' => $this->faker->unique()->firstName()
        ];
    }
}
