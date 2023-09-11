<?php

declare(strict_types=1);

namespace Kelnik\Menu\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Menu\Models\Menu;

final class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::withoutEvents(function () {
            Menu::factory()
                    ->count(rand(2, 4))
                    ->hasItems(rand(10, 50))
                    ->create();
        });
    }
}
