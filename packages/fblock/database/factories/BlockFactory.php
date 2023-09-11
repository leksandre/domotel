<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kelnik\FBlock\Models\Button;
use Kelnik\FBlock\Models\FlatBlock;

final class BlockFactory extends Factory
{
    protected $model = FlatBlock::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->company(),
            'active' => $this->faker->boolean(),
            'area' => $this->faker->unique()->sentence(),
            'floor' => $this->faker->unique()->sentence(),
            'price' => $this->faker->unique()->sentence(),
            'button' => new Button(0, ''),
            'features' => $this->faker->randomElements(
                [
                    trans('kelnik-fblock::factory.blocks.loggia'),
                    trans('kelnik-fblock::factory.blocks.highCeilings'),
                    trans('kelnik-fblock::factory.blocks.finishing'),
                    trans('kelnik-fblock::factory.blocks.kitchenLivingRoom')
                ],
                rand(0, 3)
            )
        ];
    }
}
