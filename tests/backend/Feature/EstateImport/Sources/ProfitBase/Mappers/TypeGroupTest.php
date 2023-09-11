<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\ProfitBase\Mappers;

use Kelnik\EstateImport\Models\Proxy\PremisesTypeGroup;
use Kelnik\EstateImport\Sources\ProfitBase\Mappers\TypeGroup;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class TypeGroupTest extends AbstractMapper
{
    protected string $mapperClassName = TypeGroup::class;

    public static function mapperProvider(): array
    {
        return [
            [
                'row' => [
                    "id" => 117141,
                    "alias" => "property",
                    "name" => "Квартира",
                    "typePurpose" => "residential",
                    "customFields" => [],
                    "systemFields" => [],
                    "nameForms" => [
                        "plural" => "Квартиры",
                        "genitive" => "Квартиры",
                        "genitivePlural" => "Квартир",
                        "accusative" => "Квартиру",
                        "abbreviation" => "Кв"
                    ]
                ],
                'res' => [
                    PremisesTypeGroup::class => [
                        'external_id' => 'residential__property',
                        'living' => true,
                        'title' => 'Квартира',
                        'slug' => 'kvartira-117141'
                    ]
                ]
            ]
        ];
    }
}
