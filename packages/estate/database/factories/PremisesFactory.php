<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Estate\Models\Premises;

final class PremisesFactory extends Factory
{
    protected $model = Premises::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->randomNumber();

        return [
            'title' => $title,
            'number' => $title,
            'active' => $this->faker->boolean(),
            'action' => $this->faker->boolean(),
            'external_id' => $this->faker->unique()->uuid(),
            'hash' => $this->faker->unique()->md5()
        ];
    }
}
