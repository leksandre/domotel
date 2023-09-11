<?php

declare(strict_types=1);

namespace Kelnik\Form\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Form\Models\Field;

final class FieldFactory extends Factory
{
    protected $model = Field::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'active' => $this->faker->boolean()
        ];
    }
}
