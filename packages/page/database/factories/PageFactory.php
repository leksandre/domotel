<?php

declare(strict_types=1);

namespace Kelnik\Page\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Kelnik\Page\Models\Enums\Type;
use Kelnik\Page\Models\Page;

final class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(2, true);

        return [
            'active' => $this->faker->boolean(),
            'type' => Type::Simple,
            'title' => $title,
            'slug' => Str::slug($title)
        ];
    }
}
