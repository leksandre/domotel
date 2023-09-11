<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\Csv;

use Illuminate\Support\Carbon;
use Kelnik\EstateImport\Models\Proxy\Building;
use Kelnik\EstateImport\Models\Proxy\Completion;
use Kelnik\EstateImport\Models\Proxy\Complex;
use Kelnik\EstateImport\Models\Proxy\Floor;
use Kelnik\EstateImport\Models\Proxy\Premises;
use Kelnik\EstateImport\Models\Proxy\PremisesStatus;
use Kelnik\EstateImport\Models\Proxy\PremisesType;
use Kelnik\EstateImport\Models\Proxy\PremisesTypeGroup;
use Kelnik\EstateImport\Models\Proxy\Section;
use Kelnik\EstateImport\Sources\Csv\Mapper;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class MapperTest extends AbstractMapper
{
    protected string $mapperClassName = Mapper::class;

    public static function mapperProvider(): array
    {
        $dates = [
            '2024-03-25',
            '2025-04-05',
            ''
        ];

        return [
            'Flat' => [
                'row' => [
                    '1',
                    1,
                    76.2,
                    0,
                    0,
                    2,
                    1,
                    7550000,
                    'Корней',
                    '1',
                    'А',
                    '2',
                    'квартира',
                    'двухкмонатная',
                    Carbon::createFromFormat('Y-m-d', $dates[0]),
                    '',
                    '',
                    ''
                ],
                'res' => [
                    Complex::class => [
                        'title' => 'Корней',
                        "external_id" => 'корней',
                        'hash' => '45d003a116a58eb28072b3a34783213d'
                    ],
                    Completion::class => [
                        'title' => '1 кв. 2024',
                        'event_date' => Carbon::createFromFormat('Y-m-d', $dates[0]),
                        'external_id' => $dates[0]
                    ],
                    Building::class => [
                        Building::REF_COMPLEX => 'корней',
                        Building::REF_COMPLETION => $dates[0],
                        'title' => '1',
                        'external_id' => 'корней_1',
                        'hash' => '5df1a3f7cdf4f2d30e1208869baafd32'
                    ],
                    Section::class => [
                        Section::REF_BUILDING => 'корней_1',
                        'title' => 'А',
                        'external_id' => 'корней_1_а',
                        'hash' => '070a9750a81fafcd8fd655b8aa2d5520'
                    ],
                    Floor::class => [
                        Floor::REF_BUILDING => 'корней_1',
                        'title' => '2',
                        'number' => 2,
                        'external_id' => 'корней_1_2',
                        'hash' => '0ad878cca045be958757466843c2b999'
                    ],
                    PremisesStatus::class => [
                        'title' => 1,
                        'external_id' => 1
                    ],
                    PremisesTypeGroup::class => [
                        'title' => 'квартира',
                        'external_id' => 'квартира'
                    ],
                    PremisesType::class => [
                        'title' => 'двухкмонатная',
                        PremisesType::REF_GROUP => 'квартира',
                        'external_id' => 'квартира-двухкмонатная'
                    ],
                    Premises::class => [
                        Premises::REF_FLOOR => 'корней_1_2',
                        Premises::REF_SECTION => 'корней_1_а',
                        Premises::REF_TYPE => 'квартира-двухкмонатная',
                        Premises::REF_STATUS => 1,
                        'external_id' => '1',
                        'title' => 1,
                        'number' => 1,
                        'area_total' => 76.2,
                        'area_living' => 0,
                        'area_kitchen' => 0,
                        'rooms' => 2,
                        'price_total' => 7550000,
                        Premises::REF_IMAGE_PLAN => null,
                        Premises::REF_FEATURES => [],
                        'planoplan_code' => '',
                        'hash' => '81db51edbc29797246d53e40820a235c'
                    ]
                ]
            ],
            'Parking place' => [
                'row' => [
                    '64-п',
                    64,
                    15.7,
                    0,
                    0,
                    0,
                    1,
                    1070000,
                    'Корней',
                    '1',
                    'А',
                    '-1',
                    'паркинг',
                    's',
                    Carbon::createFromFormat('Y-m-d', $dates[1]),
                    '',
                    '',
                    ''
                ],
                'res' => [
                    Complex::class => [
                        'title' => 'Корней',
                        'external_id' => 'корней',
                        'hash' => '45d003a116a58eb28072b3a34783213d'
                    ],
                    Completion::class => [
                        'title' => '2 кв. 2025',
                        'event_date' => Carbon::createFromFormat('Y-m-d', $dates[1]),
                        'external_id' => $dates[1]
                    ],
                    Building::class => [
                        Building::REF_COMPLEX => 'корней',
                        Building::REF_COMPLETION => $dates[1],
                        'title' => '1',
                        'external_id' => 'корней_1',
                        'hash' => '24435f60ac86a519d38ea4d613d7342b'
                    ],
                    Section::class => [
                        Section::REF_BUILDING => 'корней_1',
                        'title' => 'А',
                        'external_id' => 'корней_1_а',
                        'hash' => '070a9750a81fafcd8fd655b8aa2d5520'
                    ],
                    Floor::class => [
                        Floor::REF_BUILDING => 'корней_1',
                        'title' => '-1',
                        'number' => -1,
                        'external_id' => 'корней_1_-1',
                        'hash' => 'fdc96be2cb2a75055e1222a0f4c51ba0'
                    ],
                    PremisesStatus::class => [
                        'title' => 1,
                        'external_id' => 1
                    ],
                    PremisesTypeGroup::class => [
                        'title' => 'паркинг',
                        'external_id' => 'паркинг'
                    ],
                    PremisesType::class => [
                        'title' => 's',
                        PremisesType::REF_GROUP => 'паркинг',
                        'external_id' => 'паркинг-s'
                    ],
                    Premises::class => [
                        Premises::REF_FLOOR => 'корней_1_-1',
                        Premises::REF_SECTION => 'корней_1_а',
                        Premises::REF_TYPE => 'паркинг-s',
                        Premises::REF_STATUS => 1,
                        'external_id' => '64-п',
                        'title' => 64,
                        'number' => 64,
                        'area_total' => 15.7,
                        'area_living' => 0,
                        'area_kitchen' => 0,
                        'rooms' => 0,
                        'price_total' => 1070000,
                        Premises::REF_IMAGE_PLAN => null,
                        Premises::REF_FEATURES => [],
                        'planoplan_code' => '',
                        'hash' => '4777be1a9705929c75e304cb02bbf254'
                    ]
                ]
            ],
            'Pantry' => [
                'row' => [
                    '49-к',
                    49,
                    4.4,
                    0,
                    0,
                    0,
                    1,
                    330000,
                    'Корней',
                    '2',
                    'Б',
                    '-1',
                    'кладовка',
                    's',
                    $dates[2],
                    '',
                    '',
                    ''
                ],
                'res' => [
                    Complex::class => [
                        'title' => 'Корней',
                        'external_id' => 'корней',
                        'hash' => '45d003a116a58eb28072b3a34783213d'
                    ],
                    Building::class => [
                        Building::REF_COMPLEX => 'корней',
                        Building::REF_COMPLETION => $dates[2],
                        'title' => '2',
                        'external_id' => 'корней_2',
                        'hash' => 'f4b382b114a4114f2b5b8c66c285c843'
                    ],
                    Section::class => [
                        Section::REF_BUILDING => 'корней_2',
                        'title' => 'Б',
                        'external_id' => 'корней_2_б',
                        'hash' => 'f1fd93d4f45f5e8da6c242cb2460c439'
                    ],
                    Floor::class => [
                        Floor::REF_BUILDING => 'корней_2',
                        'title' => '-1',
                        'number' => -1,
                        'external_id' => 'корней_2_-1',
                        'hash' => '2661c7283311476e5afe38a9f5a62e62'
                    ],
                    PremisesStatus::class => [
                        'title' => 1,
                        'external_id' => 1
                    ],
                    PremisesTypeGroup::class => [
                        'title' => 'кладовка',
                        'external_id' => 'кладовка'
                    ],
                    PremisesType::class => [
                        'title' => 's',
                        PremisesType::REF_GROUP => 'кладовка',
                        'external_id' => 'кладовка-s'
                    ],
                    Premises::class => [
                        Premises::REF_FLOOR => 'корней_2_-1',
                        Premises::REF_SECTION => 'корней_2_б',
                        Premises::REF_TYPE => 'кладовка-s',
                        Premises::REF_STATUS => 1,
                        'external_id' => '49-к',
                        'title' => 49,
                        'number' => 49,
                        'area_total' => 4.4,
                        'area_living' => 0,
                        'area_kitchen' => 0,
                        'rooms' => 0,
                        'price_total' => 330000,
                        Premises::REF_IMAGE_PLAN => null,
                        Premises::REF_FEATURES => [],
                        'planoplan_code' => '',
                        'hash' => '063728bce1d321572b9c41394de894da'
                    ]
                ]
            ]
        ];
    }
}
