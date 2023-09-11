<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources\ProfitBase\Mappers;

use Kelnik\EstateImport\Models\Proxy\PremisesStatus;
use Kelnik\EstateImport\Sources\ProfitBase\Mappers\Status;
use Kelnik\Tests\Feature\EstateImport\Sources\AbstractMapper;

final class StatusTest extends AbstractMapper
{
    protected string $mapperClassName = Status::class;

    public static function mapperProvider(): array
    {
        return [
            [
                'row' => [
                    "id" => 123476,
                    "name" => "Свободно",
                    "color" => "#63cba5",
                    "baseStatus" => "AVAILABLE",
                    "isProtected" => true,
                    "alias" => "AVAILABLE"
                ],
                'res' => [
                    PremisesStatus::class => [
                        'external_id' => 123476,
                        'title' => 'Свободно',
                        'color' => '#63cba5'
                    ]
                ]
            ]
        ];
    }
}
