<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Estate\Models\PremisesFeatureGroup;

final class PremisesFeatureGroupFactory extends Factory
{
    protected $model = PremisesFeatureGroup::class;

    public function definition(): array
    {
        return [
            'active' => $this->faker->boolean(),
            'external_id' => $this->faker->unique()->uuid()
        ];
    }
}
