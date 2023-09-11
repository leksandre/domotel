<?php

declare(strict_types=1);

namespace Kelnik\News\Database\Factories;

use Illuminate\Support\Str;
use Kelnik\News\Models\Element;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ElementFactory extends Factory
{
    protected $model = Element::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->realText(50);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'active' => $this->faker->boolean(),
            'publish_date' => $this->faker->dateTimeThisYear(),
            'preview' => $this->faker->unique()->realText(100),
            'body' => $this->faker->unique()->realText()
        ];
    }
}
