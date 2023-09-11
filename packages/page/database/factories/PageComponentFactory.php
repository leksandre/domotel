<?php

declare(strict_types=1);

namespace Kelnik\Page\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;

final class PageComponentFactory extends Factory
{
    protected $model = PageComponent::class;

    public function definition(): array
    {
        /** @var Collection $components */
        $components = resolve(BladeComponentRepository::class)->getList();

        return [
            'active' => $this->faker->boolean(),
            'component' => $components->random()->getComponentNamespace()
        ];
    }
}
