<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\Allio\Mappers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Kelnik\EstateImport\Models\Proxy\Building;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Allio\SourceType;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class BuildingTest extends AbstractMapper
{
    use RefreshDatabase;

    private const COMPLEX_ID = 1;
    protected string $mapperClassName = \Kelnik\EstateImport\Sources\Allio\Mappers\Building::class;

    protected function setUp(): void
    {
        parent::setUp();

        resolve(ImportSettingsService::class)->saveSourceParams(
            new SourceType(),
            ['complex' => self::COMPLEX_ID]
        );
    }

    public static function mapperProvider(): array
    {
        $title = 'Квартал 100 by AKVILON 1 корпус';
        $id = 108;

        return [
            [
                'row' => [
                    'name' => $title,
                    'id' => $id
                ],
                'res' => [
                    Building::class => [
                        'complex_id' => self::COMPLEX_ID,
                        'title' => $title,
                        'external_id' => $id
                    ]
                ]
            ]
        ];
    }
}
