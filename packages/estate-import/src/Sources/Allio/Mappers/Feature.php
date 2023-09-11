<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio\Mappers;

use Kelnik\EstateImport\Models\Proxy\PremisesFeature;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\Sources\Allio\Contracts\HasMultiValues;
use Kelnik\EstateImport\ValueExtractors\ArrayValueExtractor;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;

final class Feature extends Mapper implements HasMultiValues
{
    public function __invoke(): array
    {
        return [
            PremisesFeature::class => [
                'external_id' => fn(MapperDto $dto) => $this->replaceExternalId(
                    (new StringValueExtractor())($dto->source['type'] ?? '')
                ),
                'title' => 'name',
            ]
        ];
    }

    public function getElements(MapperDto $dto): array
    {
        return (new ArrayValueExtractor())($dto->source['features'] ?? []);
    }
}
