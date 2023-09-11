<?php

declare(strict_types=1);

namespace Kelnik\Progress\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Kelnik\Progress\Models\Camera;

final class CameraFactory extends Factory
{
    protected $model = Camera::class;

    public function __construct(
        $count = null,
        ?Collection $states = null,
        ?Collection $has = null,
        ?Collection $for = null,
        ?Collection $afterMaking = null,
        ?Collection $afterCreating = null,
        $connection = null
    ) {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

        $this->faker->addProvider(new YouTubeFaker($this->faker));
        $this->faker->addProvider(new VimeoFaker($this->faker));
    }

    public function definition(): array
    {
        return [
            'active' => $this->faker->boolean(),
            'title' => $this->faker->sentence(5, true),
            'url' => $this->faker->randomElement([
                $this->faker->unique()->youTubeEmbedLink(),
                $this->faker->unique()->vimeoEmbedLink()
            ])
        ];
    }
}
