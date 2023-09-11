<?php

declare(strict_types=1);

namespace Kelnik\Form\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Form\Models\Form;

final class FormSeeder extends Seeder
{
    public function run(): void
    {
        Form::withoutEvents(function () {
            Form::factory()
                    ->count(rand(2, 4))
                    ->hasFields(rand(3, 10))
                    ->hasEmails(rand(1, 5))
                    ->create();
        });
    }
}
