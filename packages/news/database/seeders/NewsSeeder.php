<?php

declare(strict_types=1);

namespace Kelnik\News\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\News\Models\Category;

final class NewsSeeder extends Seeder
{
    public function run(): void
    {
        Category::factory()
            ->count(rand(2, 4))
            ->hasElements(rand(10, 50))
            ->createQuietly();
    }
}
