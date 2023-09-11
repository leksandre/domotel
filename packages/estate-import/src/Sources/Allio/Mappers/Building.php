<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio\Mappers;

use Illuminate\Support\Arr;
use Kelnik\EstateImport\Models\Proxy\Building as BuildingProxy;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Allio\SourceType;
use Kelnik\EstateImport\ValueExtractors\IntValueExtractor;

final class Building extends Mapper
{
    public function __invoke(): array
    {
        return [
            BuildingProxy::class => [
                'complex_id' => fn(MapperDto $dto) => $this->getComplexId(),
                'external_id' => fn(MapperDto $dto) => $this->replaceExternalId(
                    (new IntValueExtractor())($dto->source['id'] ?? 0)
                ),
                'title' => 'name'
            ]
        ];
    }

    private function getComplexId(): int
    {
        return (int)Arr::get(
            resolve(ImportSettingsService::class)->getSourceParams(new SourceType()),
            'complex'
        );
    }
}
