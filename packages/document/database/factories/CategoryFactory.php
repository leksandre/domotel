<?php

declare(strict_types=1);

namespace Kelnik\Document\Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Document\Models\Category;

final class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(2, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'active' => $this->faker->boolean()
        ];
    }
}
