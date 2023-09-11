<?php

declare(strict_types=1);

namespace Kelnik\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class HostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'host' => $this->faker->domainName()
        ];
    }
}
