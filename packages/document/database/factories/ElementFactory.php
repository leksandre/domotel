<?php

declare(strict_types=1);

namespace Kelnik\Document\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Document\Models\Element;

final class ElementFactory extends Factory
{
    protected $model = Element::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence,
            'author' => $this->faker->name(),
            'publish_date' => $this->faker->dateTimeThisYear(),
            'active' => $this->faker->boolean()
        ];
    }
}
