<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Mortgage\Models\Bank;

final class BankFactory extends Factory
{
    protected $model = Bank::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->company(),
            'active' => $this->faker->boolean(),
            'link' => $this->faker->unique()->url(),
            'description' => $this->faker->unique()->realText()
        ];
    }
}
