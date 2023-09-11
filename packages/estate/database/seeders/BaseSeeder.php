<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Estate\Models\PremisesType;

abstract class BaseSeeder extends Seeder
{
    protected function getFlatPrice(PremisesType $type): float
    {
        return $this->getValue(
            [
                fn() => rand(3_200_000, 5_700_000), // Studio
                fn() => rand(5_100_000, 7_400_000), // 1
                fn() => rand(6_800_000, 10_300_000), // 2
                fn() => rand(8_500_000, 15_800_000), // 3
                fn() => rand(20_100_000, 30_000_000), // 4
                fn() => rand(27_100_000, 42_200_000) // 5
            ],
            $type->rooms
        );
    }

    protected function getFlatArea(PremisesType $type): float
    {
        return $this->getValue(
            [
                fn() => rand(17_00, 35_00) / 100, // Studio
                fn() => rand(31_00, 56_00) / 100, // 1
                fn() => rand(46_00, 70_00) / 100, // 2
                fn() => rand(65_00, 86_00) / 100, // 3
                fn() => rand(70_00, 100_00) / 100, // 4
                fn() => rand(82_00, 120_00) / 100 // 5
            ],
            $type->rooms
        );
    }

    protected function getFlatAreaKitchen(PremisesType $type): float
    {
        return $this->getValue(
            [
                0,
                fn() => rand(7_00, 15_00) / 100, // 1
                fn() => rand(10_00, 15_00) / 100, // 2
                fn() => rand(10_00, 15_00) / 100, // 3
                fn() => rand(12_00, 20_00) / 100, // 4
                fn() => rand(15_00, 25_00) / 100 // 5
            ],
            $type->rooms
        );
    }

    protected function getCommerceArea(): float
    {
        return rand(45_00, 340_00) / 100;
    }

    protected function getCommercePrice(float $area): float
    {
        return ceil($area * rand(150_000, 350_000));
    }

    protected function getParkingArea(PremisesType $type): float
    {
        return $this->getValue(
            [
                'S' => fn() => rand(13_0, 14_0) / 10,
                'M' => fn() => rand(14_1, 16_0) / 10,
                'L' => fn() => rand(23_0, 24_0) / 10,
            ],
            $type->short_title
        );
    }

    protected function getParkingPrice(float $area): float
    {
        return ceil($area * rand(30_000, 65_000));
    }

    private function getValue(array $variants, $key)
    {
        return isset($variants[$key])
            ? is_callable($variants[$key]) ? call_user_func($variants[$key]) : $variants[$key]
            : 0;
    }
}
