<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Estate\Models\PremisesTypeGroup;

final class PremisesTypeGroupFactory extends Factory
{
    protected $model = PremisesTypeGroup::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->unique()->uuid()
        ];
    }
}
