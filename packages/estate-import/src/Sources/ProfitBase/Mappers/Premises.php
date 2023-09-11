<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Mappers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kelnik\EstateImport\Models\Proxy\Floor;
use Kelnik\EstateImport\Models\Proxy\PremisesType;
use Kelnik\EstateImport\Models\Proxy\Section;
use Kelnik\EstateImport\Models\Proxy\Premises as PremisesProxy;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\Services\Contracts\DownloadService;
use Kelnik\EstateImport\ValueExtractors\ArrayValueExtractor;
use Kelnik\EstateImport\ValueExtractors\FloatValueExtractor;
use Kelnik\EstateImport\ValueExtractors\IntValueExtractor;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;

final class Premises extends Mapper
{
    use FeatureTrait;

    public function __invoke(): array
    {
        $intValue = new IntValueExtractor();
        $floatValue = new FloatValueExtractor();

        return [
            Floor::class => [
                Floor::REF_BUILDING => fn(MapperDto $dto) => $this->getBuildingExternalId($dto),
                'external_id' => fn(MapperDto $dto) => $this->getFloorExternalId($dto),
                'title' => 'floor',
                'number' => fn(MapperDto $dto) => $intValue($dto->source['floor'] ?? 1)
            ],
            Section::class => [
                Section::REF_BUILDING => fn(MapperDto $dto) => $this->getBuildingExternalId($dto),
                'external_id' => fn(MapperDto $dto) => $this->getSectionExternalId($dto),
                'title' => fn(MapperDto $dto) => $this->getSectionTitle($dto),
            ],
            PremisesType::class => [
                'group' => fn(MapperDto $dto) => $this->getTypeGroupExternalId($dto),
                'external_id' => fn(MapperDto $dto) => $this->getTypeExternalId($dto),
                'title' => fn(MapperDto $dto) => $this->getTypeTitle($dto),
                'short_title' => fn(MapperDto $dto) => $this->getTypeShortTitle($dto),
                'rooms' => fn(MapperDto $dto) => $intValue($dto->source['rooms_amount'] ?? 0),
                'slug' => fn(MapperDto $dto) => Str::slug($dto->result['title'] . '-'),
            ],
            PremisesProxy::class => [
                PremisesProxy::REF_FLOOR => fn(MapperDto $dto) => $this->getFloorExternalId($dto),
                PremisesProxy::REF_SECTION => fn(MapperDto $dto) => $this->getSectionExternalId($dto),
                PremisesProxy::REF_TYPE => fn(MapperDto $dto) => $this->getTypeExternalId($dto),
                PremisesProxy::REF_STATUS => fn(MapperDto $dto) => $intValue($dto->source['customStatusId'] ?? 0),
                'external_id' => fn(MapperDto $dto) => $intValue($dto->source['id'] ?? 0),

                'title' => 'number',
                'number' => 'number',
                'number_on_floor' => fn(MapperDto $dto) => $this->getNumberOnFloor($dto),
                'rooms' => fn(MapperDto $dto) => $intValue($dto->source['rooms_amount'] ?? 0),
                'area_total' => fn(MapperDto $dto) => $floatValue(
                    Arr::get($this->getArea($dto), 'area_total', 0)
                ),
                'area_living' => fn(MapperDto $dto) => $floatValue(
                    Arr::get($this->getArea($dto), 'area_living', 0)
                ),
                'area_kitchen' => fn(MapperDto $dto) => $floatValue(
                    Arr::get($this->getArea($dto), 'area_kitchen', 0)
                ),
                'price_total' => fn(MapperDto $dto) => $floatValue(Arr::get($dto->source, 'price.value', 0)),
                'price_meter' => fn(MapperDto $dto) => $floatValue(Arr::get($dto->source, 'price.pricePerMeter', 0)),
                PremisesProxy::REF_IMAGE_PLAN => fn(MapperDto $dto) => $this->getFilePathAndHash($dto),
                'plan_type_string' => fn(MapperDto $dto) => $this->getPlanType($dto),
                PremisesProxy::REF_FEATURES => fn(MapperDto $dto) => $this->getFeatures($dto),
                'hash' => fn(MapperDto $obj) => $this->getPremisesHash($obj)
            ]
        ];
    }

    private function getBuildingExternalId(MapperDto $dto): int
    {
        return $this->replaceExternalId(
            (new IntValueExtractor())($dto->source['house_id'])
        );
    }

    private function getFloorExternalId(MapperDto $dto): string
    {
        return $this->replaceExternalId(
            $this->getBuildingExternalId($dto) . '__' . (new IntValueExtractor())($dto->source['floor'] ?? 1)
        );
    }

    private function getSectionTitle(MapperDto $dto): string
    {
        return (new StringValueExtractor())($dto->source['section']);
    }

    private function getSectionExternalId(MapperDto $dto): string
    {
        $title = mb_strtolower($this->getSectionTitle($dto));

        return mb_strlen($title)
            ? $this->replaceExternalId($this->getBuildingExternalId($dto) . '__' . $title)
            : '';
    }

    private function getTypeGroupExternalId(MapperDto $dto): string
    {
        $strValue = new StringValueExtractor();

        return $this->replaceExternalId(
            $strValue($dto->source['typePurpose'] ?? '') . '__' . $strValue($dto->source['propertyType'] ?? '')
        );
    }

    private function getTypeExternalId(MapperDto $dto): string
    {
        $intValue = new IntValueExtractor();

        return $this->replaceExternalId(
            implode(
                '_',
                [
                    $this->getTypeGroupExternalId($dto),
                    $intValue($dto->source['rooms_amount'] ?? 0),
                    $intValue($dto->source['studio'] ?? 0)
                ]
            )
        );
    }

    private function typeIsLiving(MapperDto $dto): bool
    {
        return (new StringValueExtractor())($dto->source['typePurpose'] ?? '') === 'residential';
    }

    private function typeIsFlat(MapperDto $dto): bool
    {
        return (new StringValueExtractor())($dto->source['propertyType'] ?? '') === 'property';
    }

    private function getTypeTitle(MapperDto $dto, bool $isShort = false): string
    {
        if (!$this->typeIsLiving($dto) || !$this->typeIsFlat($dto)) {
            return trans('kelnik-estate-import::profitbase.type.non-residential');
        }

        $intValue = new IntValueExtractor();

        if ($intValue(Arr::get($dto->source, 'studio'))) {
            return trans('kelnik-estate-import::profitbase.type.studio.title');
        }

        return trans(
            $isShort
                ? 'kelnik-estate-import::profitbase.type.rooms.short'
                : 'kelnik-estate-import::profitbase.type.rooms.title',
            ['cnt' => $intValue($dto->source['rooms_amount'])]
        );
    }

    private function getTypeShortTitle(MapperDto $dto): string
    {
        return $this->getTypeTitle($dto, true);
    }

    public function getNumberOnFloor(MapperDto $dto): ?int
    {
        return (new IntValueExtractor())(
            Arr::get($dto->source, 'attributes.position_on_floor', 0)
        ) ?: null;
    }

    private function getArea(MapperDto $dto): array
    {
        return (new ArrayValueExtractor())($dto->source['area'] ?? []);
    }

    private function getPlanType(MapperDto $dto): string
    {
        return (new StringValueExtractor())(Arr::get($dto->source, 'attributes.code', ''));
    }

    public function getFeatures(MapperDto $dto): array
    {
        $customFields = (new ArrayValueExtractor())($dto->source['custom_fields'] ?? []);

        if (!$customFields) {
            return [];
        }

        $res = [];

        foreach ($customFields as $propertyData) {
            if (Arr::get($propertyData, 'value') === null || !$this->isAllowedFeature($propertyData['id'] ?? '')) {
                continue;
            }

            $res[] = $this->getFeatureExternalId($propertyData);
        }

        return $res;
    }

    private function getFilePathAndHash(MapperDto $dto): ?array
    {
        return $dto->source['preset']
            ? resolve(
                DownloadService::class,
                ['logger' => $dto->logger, 'storage' => $dto->storage, 'dirPath' => $dto->filesDirPath]
            )->download($dto->source['preset'])
            : null;
    }
}
