<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\ProfitBase\Mappers;

use Illuminate\Support\Carbon;
use Kelnik\EstateImport\Models\Proxy\Building;
use Kelnik\EstateImport\Models\Proxy\Completion;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class BuildingTest extends AbstractMapper
{
    protected string $mapperClassName = \Kelnik\EstateImport\Sources\ProfitBase\Mappers\Building::class;

    public static function mapperProvider(): array
    {
        return [
            [
                'row' => [
                    "id" => 99445,
                    "projectId" => 38087,
                    "projectName" => "Meltzer Hall",
                    "title" => "Meltzer Hall",
                    "type" => "RESIDENTIAL",
                    "street" => "Набережная реки Карповки",
                    "number" => "27",
                    "facing" => "Whitebox",
                    "material" => "Монолитно-кирпичный",
                    "buildingState" => "UNFINISHED",
                    "developmentStartQuarter" => [
                        "year" => "2019",
                        "quarter" => 2
                    ],
                    "developmentEndQuarter" => [
                        "year" => "2023",
                        "quarter" => 2
                    ],
                    "salesStart" => [
                        "month" => "01",
                        "year" => "2019"
                    ],
                    "salesEnd" => null,
                    "image" => "https://pb14774.profitbase.ru/media/cache/resolve/' .
                        'house_588_400/uploads/house/14774/637e118c2240d.jpg",
                    "minFloor" => "1",
                    "maxFloor" => "8",
                    "currency" => [
                        "id" => 1,
                        "code" => "RUB",
                        "class" => "rub",
                        "symbol" => "p",
                        "title" => "Рубли",
                        "shortName" => "руб",
                        "unicodeSymbol" => "8381"
                    ],
                    "address" => [
                        "full" => "Санкт-Петербург, Петроградский район, Санкт-Петербург Набережная реки Карповки 27",
                        "locality" => "Санкт-Петербург",
                        "district" => "Петроградский район",
                        "region" => "Санкт-Петербург",
                        "street" => "Набережная реки Карповки",
                        "number" => "27"
                    ],
                    "minPrice" => 12839264,
                    "minPriceArea" => 239526,
                    "propertyCount" => "179",
                    "countFilteredProperty" => "179",
                    "houseBadges" => [
                    ],
                    "propertyTypes" => [
                        117141,
                        117147
                    ],
                    "roomsFilter" => [
                        "one",
                        "without_layout",
                        "two",
                        "three",
                        "more_than_three"
                    ],
                    "landNumber" => null,
                    "hasAvailableProperties" => true,
                    "hasBookedProperties" => false,
                    "contractAddress" => null,
                    "externalId" => "__undefined__",
                    "showroom" => true
                ],
                'res' => [
                    Completion::class => [
                        'title' => '2 кв. 2023',
                        'event_date' => Carbon::create(2023, 6, 1),
                        'external_id' => '2023-2'
                    ],
                    Building::class => [
                        Building::REF_COMPLEX => 38087,
                        Building::REF_COMPLETION => '2023-2',
                        'external_id' => 99445,
                        'title' => 'Meltzer Hall',
                        'floor_min' => 1,
                        'floor_max' => 8
                    ]
                ]
            ]
        ];
    }
}
