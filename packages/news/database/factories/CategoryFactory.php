<?php

declare(strict_types=1);

namespace Kelnik\News\Database\Factories;

use Illuminate\Support\Str;
use Kelnik\News\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(2, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'active' => $this->faker->boolean(),
        ];
    }
}
