<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\ProfitBase\Mappers;

use Kelnik\EstateImport\Models\Proxy\Floor;
use Kelnik\EstateImport\Models\Proxy\Premises;
use Kelnik\EstateImport\Models\Proxy\PremisesType;
use Kelnik\EstateImport\Models\Proxy\Section;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class PremisesTest extends AbstractMapper
{
    protected string $mapperClassName = \Kelnik\EstateImport\Sources\ProfitBase\Mappers\Premises::class;

    public static function mapperProvider(): array
    {
        return [
            [
                'row' => [
                    "id" => 10301513,
                    "house_id" => 99445,
                    "number" => "3.1",
                    "rooms_amount" => 1,
                    "floor" => 3,
                    "studio" => false,
                    "free_layout" => false,
                    "euro_layout" => false,
                    "propertyType" => "property",
                    "typePurpose" => "residential",
                    "has_related_preset_with_layout" => true,
                    "externalId" => null,
                    "area" => [
                        "area_total" => "68.40",
                        "area_estimated" => null,
                        "area_living" => null,
                        "area_kitchen" => null,
                        "area_balcony" => null,
                        "area_without_balcony" => null
                    ],
                    "paymentTypes" => [],
                    "houseName" => "Meltzer Hall",
                    "sectionName" => "Парадная 1",
                    "projectName" => "Meltzer Hall",
                    "specialOffers" => [
                        [
                            "id" => 15124,
                            "name" => "Двор",
                            "archive" => false,
                            "color" => "#fc5f94",
                            "badgeTextColor" => "#ffffff",
                            "description" => null,
                            "descriptionActive" => false,
                            "startDate" => [
                                "date" => "2020-02-05 00:00:00.000000",
                                "timezone_type" => 3,
                                "timezone" => "Asia/Karachi"
                            ],
                            "expiration" => "",
                            "badge" => [
                                "icon" => "star",
                                "label" => "Двор"
                            ],
                            "banner" => [
                                "active" => false,
                                "text" => null,
                                "buttonText" => "Помещения по акции"
                            ],
                            "discount" => [
                                "active" => false,
                                "type" => "FULL_PRICE",
                                "unit" => "PERCENT",
                                "value" => 10,
                                "description" => false,
                                "humanizedType" => "от полной стоимости",
                                "humanizedUnit" => "%",
                                "calculate" => [
                                    "price" => 28728000,
                                    "priceForMeter" => 420000
                                ]
                            ]
                        ]
                    ],
                    "address" => "Санкт-Петербург, Петроградский район, Санкт-Петербург, Набережная реки Карповки, ' .
                        '27 - Meltzer Hall, Meltzer Hall, эт. 3, № 3.1",
                    "section" => "Парадная 1",
                    "preset" => "https://pb14774.profitbase.ru/uploads/preset/14774/63c1409c42eee.png",
                    "attachments" => [],
                    "planImages" => [
                        [
                            "source" => "https://pb14774.profitbase.ru/uploads/preset/14774/63c1409c42eee.png",
                            "imageName" => "Макет_3 эт-3.1_"
                        ],
                        [
                            "source" => "https://pb14774.profitbase.ru/uploads/preset/14774/632d772717f33.jpg",
                            "imageName" => "001_1"
                        ],
                        [
                            "source" => "https://pb14774.profitbase.ru/uploads/preset/14774/632d776a0693a.png",
                            "imageName" => "001_2"
                        ]
                    ],
                    "attributes" => [
                        "loggia_count" => null,
                        "balcony_count" => null,
                        "combined_bathroom_count" => null,
                        "separated_bathroom_count" => null,
                        "position_on_floor" => null,
                        "bti_number" => null,
                        "deposit_type" => null,
                        "bti_area" => null,
                        "window_view" => null,
                        "description" => null,
                        "code" => "3.1",
                        "facing" => null
                    ],
                    "custom_fields" => [
                        [
                            "id" => "number",
                            "name" => "Номер помещения",
                            "value" => "3.1"
                        ],
                        [
                            "id" => "property_price",
                            "name" => "Полная цена",
                            "value" => 28728000
                        ],
                        [
                            "id" => "description",
                            "name" => "Описание",
                            "value" => null
                        ],
                        [
                            "id" => "amo_responsible_name",
                            "name" => "Менеджер",
                            "value" => null
                        ],
                        [
                            "id" => "amo_contact_name",
                            "name" => "Покупатель",
                            "value" => null
                        ],
                        [
                            "id" => "owner",
                            "name" => "Владелец",
                            "value" => null
                        ],
                        [
                            "id" => "address",
                            "name" => "Адрес",
                            "value" => "Санкт-Петербург, Петроградский район, Санкт-Петербург, ' .
                                'Набережная реки Карповки, 27 - эт. 3 № 3.1"
                        ],
                        [
                            "id" => "floor_number",
                            "name" => "Этаж",
                            "value" => 3
                        ],
                        [
                            "id" => "section_name",
                            "name" => "Подъезд",
                            "value" => "Парадная 1"
                        ],
                        [
                            "id" => "house_name",
                            "name" => "Название дома",
                            "value" => "Meltzer Hall"
                        ],
                        [
                            "id" => "project_name",
                            "name" => "Название ЖК",
                            "value" => "Meltzer Hall"
                        ],
                        [
                            "id" => "property_area",
                            "name" => "Площадь, м2",
                            "value" => "68.40"
                        ],
                        [
                            "id" => "price_meter",
                            "name" => "Цена за метр",
                            "value" => 420000
                        ],
                        [
                            "id" => "rooms",
                            "name" => "Кол-во комнат",
                            "value" => 1
                        ],
                        [
                            "id" => "position_on_floor",
                            "name" => "Номер на площадке",
                            "value" => null
                        ],
                        [
                            "id" => "code",
                            "name" => "Код планировки",
                            "value" => "3.1"
                        ],
                        [
                            "id" => "building_states",
                            "name" => "Стадия строительства",
                            "value" => "Строится"
                        ],
                        [
                            "id" => "area_living",
                            "name" => "Жилая площадь, м2",
                            "value" => null
                        ],
                        [
                            "id" => "area_kitchen",
                            "name" => "Площадь кухни, м2",
                            "value" => null
                        ],
                        [
                            "id" => "area_estimated",
                            "name" => "Расчетная площадь, м2",
                            "value" => null
                        ],
                        [
                            "id" => "window",
                            "name" => "Куда выходят окна",
                            "value" => null
                        ],
                        [
                            "id" => "loggia_count",
                            "name" => "Кол-во лоджий",
                            "value" => null
                        ],
                        [
                            "id" => "balcony_count",
                            "name" => "Кол-во балконов",
                            "value" => null
                        ],
                        [
                            "id" => "combined_bathroom_count",
                            "name" => "Кол-во совмещенных санузлов",
                            "value" => null
                        ],
                        [
                            "id" => "separated_bathroom_count",
                            "name" => "Кол-во раздельных санузлов",
                            "value" => null
                        ],
                        [
                            "id" => "is_studio",
                            "name" => "Студия",
                            "value" => null
                        ],
                        [
                            "id" => "is_free_layout",
                            "name" => "Свободная планировка",
                            "value" => null
                        ],
                        [
                            "id" => "is_euro_layout",
                            "name" => "Европланировка",
                            "value" => null
                        ],
                        [
                            "id" => "facing",
                            "name" => "Отделка",
                            "value" => null
                        ],
                        [
                            "id" => "price_actual",
                            "name" => "Цена экспонирования",
                            "value" => null
                        ],
                        [
                            "id" => "payment_types",
                            "name" => "Типы оплаты",
                            "value" => null
                        ],
                        [
                            "id" => "pbcf_6267e48e2412d",
                            "name" => "Акция",
                            "value" => "повышенная комиссия 3% при продаже совместно с 3.2"
                        ],
                        [
                            "id" => "pbcf_626a8a3513777",
                            "name" => "Тип оплаты",
                            "value" => null
                        ],
                        [
                            "id" => "pbcf_6419a25948523",
                            "name" => "БАЗА",
                            "value" => null
                        ]
                    ],
                    "price" => [
                        "value" => 28728000,
                        "pricePerMeter" => 420000
                    ],
                    "status" => "AVAILABLE",
                    "customStatusId" => 123476,
                    "responsibleName" => null,
                    "responsibleId" => null,
                    "crmContactName" => null,
                    "crmContactId" => null,
                    "countHistoryRecord" => "3",
                    "countDeals" => 0,
                    "bookedUntilDate" => null,
                    "bookedUntilTime" => null,
                    "bookedAt" => null,
                    "soldAt" => null
                ],
                'res' => [
                    Floor::class => [
                        Floor::REF_BUILDING => 99445,
                        'external_id' => '99445__3',
                        'title' => 3,
                        'number' => 3
                    ],
                    Section::class => [
                        Section::REF_BUILDING => 99445,
                        'external_id' => '99445__парадная 1',
                        'title' => 'Парадная 1'
                    ],
                    PremisesType::class => [
                        PremisesType::REF_GROUP => 'residential__property',
                        'external_id' => 'residential__property_1_0',
                        'title' => '1-комн.',
                        'short_title' => '1к',
                        'rooms' => 1,
                        'slug' => '1-komn'
                    ],
                    Premises::class => [
                        Premises::REF_FLOOR => '99445__3',
                        Premises::REF_SECTION => '99445__парадная 1',
                        Premises::REF_TYPE => 'residential__property_1_0',
                        Premises::REF_STATUS => 123476,

                        'external_id' => 10301513,
                        'title' => '3.1',
                        'number' => '3.1',
                        'number_on_floor' => null,
                        'rooms' => 1,
                        'area_total' => 68.4,
                        'area_living' => 0.0,
                        'area_kitchen' => 0.0,
                        'price_total' => 28728000.0,
                        'price_meter' => 420000.0,

                        Premises::REF_IMAGE_PLAN => [
                            'hash' => '63c1409c42eee',
                            'path' => '63c1409c42eee.png'
                        ],
                        'plan_type_string' => '3.1',
                        Premises::REF_FEATURES => [
                            'pbcf_6267e48e2412d__повышенная комиссия 3% при продаже совместно с 3.2'
                        ],
                        'hash' => '5c786bb5295d258f16de3d5b29d4c5f7'
                    ]
                ]
            ]
        ];
    }
}
