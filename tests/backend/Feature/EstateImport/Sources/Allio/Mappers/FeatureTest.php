<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\Allio\Mappers;

use Faker\Factory;
use Kelnik\EstateImport\Models\Proxy\PremisesFeature;
use Kelnik\EstateImport\Sources\Allio\Mappers\Feature;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class FeatureTest extends AbstractMapper
{
    protected string $mapperClassName = Feature::class;

    public static function mapperProvider(): array
    {
        $faker = Factory::create();
        $title = $faker->title;
        $type = $faker->domainName;

        return [
            [
                'row' => [
                    'name' => $title,
                    'type' => $type
                ],
                'res' => [
                    PremisesFeature::class => [
                        'title' => $title,
                        'external_id' => $type
                    ]
                ]
            ]
        ];
    }
}
