<?php

declare(strict_types=1);

namespace Kelnik\Menu\Database\Factories;

use Kelnik\Menu\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

final class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'active' => $this->faker->boolean(),
            'link' => $this->faker->randomElement([
                null,
                fn () => $this->faker->url()
            ])
        ];
    }
}
