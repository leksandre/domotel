<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Mappers;

use Kelnik\EstateImport\Models\Proxy\PremisesFeatureGroup;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\Sources\ProfitBase\Contracts\HasMultiValues;
use Kelnik\EstateImport\ValueExtractors\ArrayValueExtractor;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;

final class FeatureGroup extends Mapper implements HasMultiValues
{
    use FeatureTrait;

    public function __invoke(): array
    {
        return [
            PremisesFeatureGroup::class => [
                'external_id' => fn(MapperDto $dto) => $this->replaceExternalId(
                    (new StringValueExtractor())($dto->source['id'] ?? '')
                ),
                'title' => 'name',
            ]
        ];
    }

    public function getElements(MapperDto $dto): array
    {
        return array_filter(
            (new ArrayValueExtractor())($dto->source['custom_fields'] ?? []),
            fn($el) => ($el['value'] ?? null) !== null && $this->isGroupFeature($el['id'] ?? '')
        );
    }
}
