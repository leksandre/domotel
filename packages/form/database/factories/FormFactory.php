<?php

declare(strict_types=1);

namespace Kelnik\Form\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Kelnik\Form\Models\Form;

final class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(2);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'active' => $this->faker->boolean(),
            'success_title' => $this->faker->unique()->sentence(3),
            'error_title' => $this->faker->unique()->sentence(3),
            'notify_title' => $this->faker->unique()->sentence(3),
            'success_text' => $this->faker->unique()->realText(10),
            'error_text' => $this->faker->unique()->realText(10),
            'description' => $this->faker->unique()->realText(10),
        ];
    }
}
