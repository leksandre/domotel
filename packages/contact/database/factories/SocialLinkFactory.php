<?php

declare(strict_types=1);

namespace Kelnik\Contact\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Contact\Models\SocialLink;

final class SocialLinkFactory extends Factory
{
    protected $model = SocialLink::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence(),
            'active' => $this->faker->boolean(),
            'link' => $this->faker->unique()->url()
        ];
    }
}
