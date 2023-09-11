<?php

declare(strict_types=1);

namespace Kelnik\Menu\Database\Factories;

use Kelnik\Menu\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

final class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(2),
            'active' => $this->faker->boolean()
        ];
    }
}
