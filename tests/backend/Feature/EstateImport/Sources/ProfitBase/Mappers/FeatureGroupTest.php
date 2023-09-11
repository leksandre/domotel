<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\ProfitBase\Mappers;

use Faker\Factory;
use Kelnik\EstateImport\Models\Proxy\PremisesFeatureGroup;
use Kelnik\EstateImport\Sources\ProfitBase\Mappers\FeatureGroup;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class FeatureGroupTest extends AbstractMapper
{
    protected string $mapperClassName = FeatureGroup::class;

    public static function mapperProvider(): array
    {
        $faker = Factory::create();
        $facingTitle = $faker->unique()->title;

        return [
            [
                'row' => [
                    "id" => "facing",
                    "name" => "Отделка",
                    "value" => $facingTitle
                ],
                'res' => [
                    PremisesFeatureGroup::class => [
                        'title' => 'Отделка',
                        'external_id' => 'facing'
                    ]
                ]
            ]
        ];
    }
}
