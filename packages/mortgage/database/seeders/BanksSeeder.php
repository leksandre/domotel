<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Mortgage\Models\Bank;

final class BanksSeeder extends Seeder
{
    public function run(): void
    {
        Bank::withoutEvents(function () {
            Bank::factory()
                    ->count(rand(2, 10))
                    ->hasPrograms(rand(2, 15))
                    ->create();
        });
    }
}
