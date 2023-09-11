<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Models\Completion;

final class CompletionFactory extends Factory
{
    protected $model = Completion::class;

    public function definition(): array
    {
        $eventDate = $this->faker->unique()->dateTimeInInterval('-1 year', '+3 years');
        $eventDate = new Carbon($eventDate);

        return [
            'event_date' => $eventDate,
            'title' => trans(
                'kelnik-estate::factory.dateQuarter',
                ['quarter' => $eventDate->quarter, 'year' => $eventDate->year]
            ),
            'external_id' => $this->faker->unique()->uuid()
        ];
    }
}
