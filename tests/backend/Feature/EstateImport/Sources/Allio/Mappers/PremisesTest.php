<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\Allio\Mappers;

use Kelnik\EstateImport\Models\Proxy\Floor;
use Kelnik\EstateImport\Models\Proxy\Premises;
use Kelnik\EstateImport\Models\Proxy\PremisesType;
use Kelnik\EstateImport\Models\Proxy\Section;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class PremisesTest extends AbstractMapper
{
    protected string $mapperClassName = \Kelnik\EstateImport\Sources\Allio\Mappers\Premises::class;

    public static function mapperProvider(): array
    {
        return [
            [
                'row' => [
                    "area" => [
                        [
                            "square" => 11.34,
                            "name" => "Комната",
                            "type" => "ROOM"
                        ],
                        [
                            "square" => 13.85,
                            "name" => "Комната",
                            "type" => "ROOM"
                        ],
                        [
                            "square" => 5.82,
                            "name" => "Коридор",
                            "type" => "HALL"
                        ],
                        [
                            "square" => 9.08,
                            "name" => "Кухня",
                            "type" => "KITCHEN"
                        ],
                        [
                            "square" => 2.75,
                            "name" => "Санузел",
                            "type" => "BATH"
                        ],
                        [
                            "square" => 1.38,
                            "name" => "Санузел",
                            "type" => "BATH"
                        ]
                    ],
                    "building_id" => 108,
                    "images" => [
                        [
                            "path" => "bb8d/523218d8cbb51b2a1f266cdae54bf5cd.png",
                            "type" => "GENERAL"
                        ],
                        [
                            "path" => "bb8d/c40e4a703b8087a23bb38a0c6e5eac7e.png",
                            "type" => "PHOTOS"
                        ]
                    ],
                    "stype" => "APARTMENT",
                    "tech_number" => "",
                    "porch" => 1,
                    "balconies" => [
                        [
                            "square" => 2.7,
                            "name" => "Лоджия",
                            "factor" => 0.5
                        ]
                    ],
                    "azimuth" => -1,
                    "number" => "45",
                    "square" => 45.57,
                    "pib_square" => 0,
                    "features" => [
                        [
                            "name" => "Раздельный санузел",
                            "type" => "SEPBATH"
                        ]
                    ],
                    "size" => [
                        "rooms" => 2,
                        "is_studio" => false,
                        "name" => "2-комнатная",
                        "type" => "CLASSIC"
                    ],
                    "sizegroup" => null,
                    "window_view" => "На улицу",
                    "price" => [
                        "promo" => 5279649,
                        "full_m2" => 121858,
                        "base" => 6533022,
                        "full" => 5553069,
                        "base_m2" => 143362
                    ],
                    "reserve" => null,
                    "modified" => "2023-04-20T00:05:08",
                    "living_square" => 0,
                    "id" => 36166,
                    "is_apartment" => false,
                    "state" => "FREE",
                    "floor" => 7,
                    "height" => 2.7
                ],
                'res' => [
                    Section::class => [
                        Section::REF_BUILDING => 108,
                        'external_id' => '108__1',
                        'title' => '1'
                    ],
                    Floor::class => [
                        Floor::REF_BUILDING => 108,
                        'external_id' => '108__7',
                        'title' => 7,
                        'number' => 7
                    ],
                    PremisesType::class => [
                        PremisesType::REF_GROUP => 'APARTMENT',
                        'external_id' => 'APARTMENT_CLASSIC_2_0',
                        'title' => '2-комнатная',
                        'short_title' => '2к',
                        'rooms' => 2,
                        'slug' => '2-komnatnaia'
                    ],
                    Premises::class => [
                        Premises::REF_FLOOR => '108__7',
                        Premises::REF_SECTION => '108__1',
                        Premises::REF_TYPE => 'APARTMENT_CLASSIC_2_0',
                        Premises::REF_STATUS => 'FREE',

                        'external_id' => 36166,
                        'title' => '2-комнатная №45',
                        'number' => '45',
                        'rooms' => 2,
                        'area_total' => 45.57,
                        'area_living' => 00.,
                        'area_kitchen' => 9.08,
                        'price_total' => 6533022.0,
                        'price_sale' => 5279649.0,
                        'price_meter' => 143362.0,

                        Premises::REF_IMAGE_PLAN => [
                            'hash' => '523218d8cbb51b2a1f266cdae54bf5cd',
                            'path' => '523218d8cbb51b2a1f266cdae54bf5cd.png'
                        ],
                        Premises::REF_FEATURES => [
                            ['external_id' => 'SEPBATH']
                        ],
                        'hash' => 'dcf1e47b1b31e80fc2cf8e21e6a77992'
                    ]
                ]
            ]
        ];
    }
}
