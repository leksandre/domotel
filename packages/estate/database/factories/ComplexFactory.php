<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Kelnik\Estate\Models\Complex;

final class ComplexFactory extends Factory
{
    protected $model = Complex::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->firstName();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'active' => $this->faker->boolean(),
            'external_id' => $this->faker->unique()->uuid(),
            'hash' => $this->faker->unique()->md5()
        ];
    }
}
