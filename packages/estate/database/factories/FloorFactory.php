<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Kelnik\Estate\Models\Floor;

final class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->randomNumber();

        return [
            'title' => $title,
            'number' => $title,
            'slug' => Str::slug($title),
            'active' => $this->faker->boolean(),
            'external_id' => $this->faker->unique()->uuid(),
            'hash' => $this->faker->unique()->md5()
        ];
    }
}
