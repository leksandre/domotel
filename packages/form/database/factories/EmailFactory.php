<?php

declare(strict_types=1);

namespace Kelnik\Form\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\Form\Models\Email;

final class EmailFactory extends Factory
{
    protected $model = Email::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->email
        ];
    }
}
