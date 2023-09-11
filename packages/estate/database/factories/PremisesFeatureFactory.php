<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Estate\Models\PremisesFeature;

final class PremisesFeatureFactory extends Factory
{
    protected $model = PremisesFeature::class;

    public function definition(): array
    {
        return [
            'active' => $this->faker->boolean(),
            'external_id' => $this->faker->unique()->uuid()
        ];
    }
}
