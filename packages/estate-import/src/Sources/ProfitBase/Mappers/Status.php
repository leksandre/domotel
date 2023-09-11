<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Mappers;

use Kelnik\EstateImport\Models\Proxy\PremisesStatus;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\ValueExtractors\IntValueExtractor;

final class Status extends Mapper
{
    public function __invoke(): array
    {
        return [
            PremisesStatus::class => [
                'external_id' => fn(MapperDto $dto) => $this->replaceExternalId(
                    (new IntValueExtractor())($dto->source['id'] ?? 0)
                ),
                'title' => 'name',
                'color' => 'color'
            ]
        ];
    }
}
