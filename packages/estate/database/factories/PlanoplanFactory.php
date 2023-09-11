<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlanoplanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => str_replace('-', '', $this->faker->uuid()),
            'active' => $this->faker->boolean
        ];
    }
}
