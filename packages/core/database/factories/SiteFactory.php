<?php

declare(strict_types=1);

namespace Kelnik\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Core\Models\Enums\Lang;
use Kelnik\Core\Models\Enums\Type;

final class SiteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'active' => $this->faker->boolean(),
            'primary' => false,
            'type' => Type::Site,
            'locale' => Lang::Russian,
            'title' => $this->faker->unique()->sentence(3)
        ];
    }
}
