<?php

declare(strict_types=1);

namespace Kelnik\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Core\Models\Setting;

final class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'module' => $this->faker->unique()->word(),
            'name' => $this->faker->unique()->word()
        ];
    }
}
