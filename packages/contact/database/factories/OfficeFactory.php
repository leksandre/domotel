<?php

declare(strict_types=1);

namespace Kelnik\Contact\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Contact\Models\Office;
use Kelnik\Core\Map\Contracts\Coords;

final class OfficeFactory extends Factory
{
    protected $model = Office::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence(),
            'active' => $this->faker->boolean(),
            'region' => $this->faker->randomElement([null, $this->faker->citySuffix()]),
            'city' => $this->faker->randomElement([null, $this->faker->unique()->city()]),
            'street' => $this->faker->randomElement([null, $this->faker->unique()->streetAddress()]),
            'phone' => $this->faker->randomElement([null, $this->faker->unique()->phoneNumber()]),
            'email' => $this->faker->randomElement([null, $this->faker->unique()->email()]),
            'route_link' => $this->faker->randomElement([null, $this->faker->unique()->url()]),
            'coords' => $this->faker->randomElement([
                resolve(Coords::class),
                resolve(Coords::class, [
                    'lat' => $this->faker->unique()->latitude(),
                    'lng' => $this->faker->unique()->longitude()
                ])
            ])
        ];
    }
}
