<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\ProfitBase\Mappers;

use Faker\Factory;
use Kelnik\EstateImport\Models\Proxy\PremisesFeature;
use Kelnik\EstateImport\Sources\ProfitBase\Mappers\Feature;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class FeatureTest extends AbstractMapper
{
    protected string $mapperClassName = Feature::class;

    public static function mapperProvider(): array
    {
        $faker = Factory::create();
        $facingTitle = $faker->unique()->title;

        return [
            'Is studio' => [
                'row' => [
                    "id" => "is_euro_layout",
                    "name" => "Студия",
                    "value" => true
                ],
                'res' => [
                    PremisesFeature::class => [
                        PremisesFeature::REF_GROUP => '',
                        'title' => 'Студия',
                        'external_id' => 'is_euro_layout'
                    ]
                ]
            ],
            'Facing' => [
                'row' => [
                    "id" => "facing",
                    "name" => "Отделка",
                    "value" => $facingTitle
                ],
                'res' => [
                    PremisesFeature::class => [
                        PremisesFeature::REF_GROUP => 'facing',
                        'title' => $facingTitle,
                        'external_id' => 'facing__' . mb_strtolower($facingTitle)
                    ]
                ]
            ]
        ];
    }
}
