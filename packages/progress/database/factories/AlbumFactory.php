<?php

declare(strict_types=1);

namespace Kelnik\Progress\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Progress\Models\Album;

final class AlbumFactory extends Factory
{
    protected $model = Album::class;

    public function definition(): array
    {
        return [
            'active' => $this->faker->boolean(),
            'title' => $this->faker->monthName(),
            'comment' => $this->faker->sentence(10, true),
            'description' => $this->faker->realText()
        ];
    }
}
