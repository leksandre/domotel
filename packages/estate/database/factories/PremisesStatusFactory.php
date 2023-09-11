<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Estate\Models\PremisesStatus;

final class PremisesStatusFactory extends Factory
{
    protected $model = PremisesStatus::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement(trans('kelnik-estate::factory.premisesStatusVariants')),
            'color' => $this->faker->unique()->hexColor(),
            'external_id' => $this->faker->unique()->uuid()
        ];
    }
}
