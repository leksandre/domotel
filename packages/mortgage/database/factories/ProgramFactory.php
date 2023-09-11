<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Mortgage\Models\Program;

final class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        $minTime = rand(1, 7);
        $minPaymentPercent = $this->faker->randomFloat(2, 0.1, 80);

        return [
            'title' => $this->faker->unique()->sentences(3, true),
            'active' => $this->faker->boolean(),
            'min_time' => $minTime,
            'max_time' => rand($minTime, 30),
            'min_payment_percent' => $minPaymentPercent,
            'max_payment_percent' => $this->faker->randomFloat(2, $minPaymentPercent, 90),
            'rate' => $this->faker->randomFloat(2, 1, 20),
            'description' => $this->faker->unique()->realText()
        ];
    }
}
