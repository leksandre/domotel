<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Mappers;

use Illuminate\Support\Carbon;
use Kelnik\EstateImport\Models\Proxy\Completion;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\ValueExtractors\ArrayValueExtractor;
use Kelnik\EstateImport\ValueExtractors\IntValueExtractor;

final class Building extends Mapper
{
    public function __invoke(): array
    {
        $intValue = new IntValueExtractor();

        return [
            Completion::class => [
                'title' => fn(MapperDto $dto) => $this->getCompletionTitle($dto),
                'event_date' => fn(MapperDto $dto) => $this->getCompletionDate($dto),
                'external_id' => fn(MapperDto $dto) => $this->getCompletionExternalId($dto)
            ],
            \Kelnik\EstateImport\Models\Proxy\Building::class => [
                \Kelnik\EstateImport\Models\Proxy\Building::REF_COMPLEX => fn(MapperDto $dto) =>
                    $this->replaceExternalId($intValue($dto->source['projectId'])),
                \Kelnik\EstateImport\Models\Proxy\Building::REF_COMPLETION => fn(MapperDto $dto) =>
                    $this->getCompletionExternalId($dto),
                'external_id' => fn(MapperDto $dto) => $this->replaceExternalId(
                    $intValue($dto->source['id'] ?? 0)
                ),
                'title' => 'title',
                'floor_min' => fn(MapperDto $dto) => $intValue($dto->source['minFloor'] ?? 0),
                'floor_max' => fn(MapperDto $dto) => $intValue($dto->source['maxFloor'] ?? 0)
            ]
        ];
    }

    private function getCompletionExternalId(MapperDto $dto): ?string
    {
        $value = (new ArrayValueExtractor())($dto->source['developmentEndQuarter']);

        return $value
            ? $this->replaceExternalId($value['year'] . '-' . $value['quarter'])
            : null;
    }

    private function getCompletionDate(MapperDto $dto): ?Carbon
    {
        $value = (new ArrayValueExtractor())($dto->source['developmentEndQuarter']);

        return $value
            ? Carbon::create($value['year'], $value['quarter'] * 3, 1)
            : null;
    }

    private function getCompletionTitle(MapperDto $dto): ?string
    {
        $value = (new ArrayValueExtractor())($dto->source['developmentEndQuarter']);

        if (!$value) {
            return null;
        }

        return trans(
            'kelnik-estate-import::profitbase.completion.title',
            [
                'quarter' => $value['quarter'],
                'year' => $value['year']
            ]
        );
    }
}
