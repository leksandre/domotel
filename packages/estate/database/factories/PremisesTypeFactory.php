<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Estate\Models\PremisesType;

final class PremisesTypeFactory extends Factory
{
    protected $model = PremisesType::class;

    public function definition(): array
    {
        return [
            'color' => $this->faker->unique()->hexColor(),
            'external_id' => $this->faker->unique()->uuid()
        ];
    }
}
