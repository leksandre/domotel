<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Mappers;

use Illuminate\Support\Str;
use Kelnik\EstateImport\Models\Proxy\PremisesTypeGroup;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;

final class TypeGroup extends Mapper
{
    public function __invoke(): array
    {
        $strValue = new StringValueExtractor();

        return [
            PremisesTypeGroup::class => [
                'external_id' => fn(MapperDto $dto) => $this->replaceExternalId(
                    $strValue($dto->source['typePurpose'] ?? '') . '__' .
                    $strValue($dto->source['alias'] ?? '')
                ),
                'living' => fn(MapperDto $dto) => $this->typeIsLiving($dto),
                'title' => 'name',
                'slug' => fn(MapperDto $dto) => Str::slug(
                    $dto->result['title'] . '-' . $strValue($dto->source['id'])
                )
            ]
        ];
    }

    private function typeIsLiving(MapperDto $dto): bool
    {
        return (new StringValueExtractor())($dto->source['typePurpose'] ?? '') === 'residential';
    }
}
