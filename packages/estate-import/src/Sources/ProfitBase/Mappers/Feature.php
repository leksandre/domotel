<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Mappers;

use Illuminate\Support\Arr;
use Kelnik\EstateImport\Models\Proxy\PremisesFeature;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\Sources\ProfitBase\Contracts\HasMultiValues;
use Kelnik\EstateImport\ValueExtractors\ArrayValueExtractor;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;

final class Feature extends Mapper implements HasMultiValues
{
    use FeatureTrait;

    public function __invoke(): array
    {
        return [
            PremisesFeature::class => [
                PremisesFeature::REF_GROUP => fn(MapperDto $dto) => $this->getGroupExternalId($dto),
                'external_id' => fn(MapperDto $dto) => $this->replaceExternalId(
                    $this->getFeatureExternalId($dto->source)
                ),
                'title' => fn(MapperDto $dto) => $this->getTitle($dto),
            ]
        ];
    }

    public function getElements(MapperDto $dto): array
    {
        return array_filter(
            (new ArrayValueExtractor())($dto->source['custom_fields'] ?? []),
            fn($el) => ($el['value'] ?? null) !== null && $this->isAllowedFeature($el['id'] ?? '')
        );
    }

    private function getGroupExternalId(MapperDto $dto): ?string
    {
        $id = (new StringValueExtractor())($dto->source['id'] ?? '');

        return !$this->isSingleFeature($id) ? $this->replaceExternalId($id) : null;
    }

    private function getTitle(MapperDto $dto): string
    {
        return (new StringValueExtractor())(
            Arr::get(
                $dto->source,
                $this->isSingleFeature((string)($dto->source['id'] ?? '')) ? 'name' : 'value',
                ''
            )
        );
    }
}
