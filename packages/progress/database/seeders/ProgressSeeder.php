<?php

declare(strict_types=1);

namespace Kelnik\Progress\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\Camera;

final class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        Album::withoutEvents(function () {
            Album::factory()
                    ->count(rand(2, 10))
                    ->hasVideos(rand(2, 15))
                    ->create();
        });

        Camera::withoutEvents(function () {
            Camera::factory()
                ->count(rand(2, 10))
                ->create();
        });
    }
}
