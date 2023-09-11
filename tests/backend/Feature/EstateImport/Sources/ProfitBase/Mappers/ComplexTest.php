<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\ProfitBase\Mappers;

use Kelnik\EstateImport\Sources\ProfitBase\Mappers\Complex;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class ComplexTest extends AbstractMapper
{
    protected string $mapperClassName = Complex::class;

    public static function mapperProvider(): array
    {
        return [
            [
                'row' => [
                    "id" => 38087,
                    "title" => "Meltzer Hall",
                    "type" => "complex",
                    "region" => "Санкт-Петербург",
                    "district" => "Петроградский район",
                    "locality" => "Санкт-Петербург",
                    "developer" => null,
                    "developer_brand" => "Alfa Faberge",
                    "banks" => "Сбер",
                    "currency" => "RUB",
                    "description" => [
                        "title" => "Клубный дом MELTZER HALL",
                        "body" => ""
                    ],
                    "youtubeVideos" => [
                    ],
                    "images" => [
                    ]
                ],
                'res' => [
                    \Kelnik\EstateImport\Models\Proxy\Complex::class => [
                        'external_id' => 38087,
                        'title' => 'Meltzer Hall'
                    ]
                ]
            ]
        ];
    }
}
