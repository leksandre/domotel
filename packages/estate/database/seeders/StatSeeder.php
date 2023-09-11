<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Seeders;

use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\Stat;

final class StatSeeder extends BaseSeeder
{
    public const PREMISES_TYPE_STUDIO = 1;
    public const PREMISES_TYPE_1ROOM = 2;
    public const PREMISES_TYPE_2ROOM = 3;
    public const PREMISES_TYPE_3ROOM = 4;
    public const PREMISES_TYPE_4ROOM = 5;

    public function run(): void
    {
        Stat::query()->truncate();

        $types = [
            self::PREMISES_TYPE_STUDIO => 0,
            self::PREMISES_TYPE_1ROOM => 1,
            self::PREMISES_TYPE_2ROOM => 2,
            self::PREMISES_TYPE_3ROOM => 3,
            self::PREMISES_TYPE_4ROOM => 4,
        ];
        $rows = [];
        $premises = [];

        foreach ($types as $typeId => $rooms) {
            $elId = 1;
            $cnt = rand(10, 25);
            $type = new PremisesType(['rooms' => $rooms]);

            // Floors
            for ($i = 0; $i <= $cnt; $i++) {
                $el = [
                    'model_row_id' => $elId,
                    'premises_type_id' => $typeId,
                    'model_name' => Floor::class,
                    'name' => 'cnt',
                    'value' => rand(1, 12)
                ];

                $premises[$typeId]['cnt'] ??= 0;
                $premises[$typeId]['cnt'] += $el['value'];

                foreach (['price', 'area'] as $name) {
                    foreach (['min', 'max'] as $suffix) {
                        $fullName = $name . '_' . $suffix;
                        $el['name'] = $fullName;
                        $newValue = $name === 'price'
                            ? $this->getFlatPrice($type)
                            : $this->getFlatArea($type);

                        $premises[$typeId][$fullName] ??= $newValue;

                        $premises[$typeId][$fullName] = call_user_func(
                            $suffix,
                            $premises[$typeId][$fullName],
                            $newValue
                        );
                        $el['value'] = $newValue;
                        $rows[] = $el;
                    }
                }

                $elId++;
            }

            foreach ([Building::class, Complex::class] as $modelName) {
                $el = [
                    'model_row_id' => 1,
                    'premises_type_id' => $typeId,
                    'model_name' => $modelName,
                    'name' => 'cnt',
                    'value' => count($premises[$typeId])
                ];
                $rows[] = $el;

                foreach (['price', 'area'] as $name) {
                    foreach (['min', 'max'] as $suffix) {
                        $fullName = $name . '_' . $suffix;
                        $el['name'] = $fullName;
                        $el['value'] = $premises[$typeId][$fullName];

                        $rows[] = $el;
                    }
                }
            }
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            Stat::query()->insert($chunk);
        }
    }
}
