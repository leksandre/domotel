<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio\Mappers;

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
    private const IMG_BASE_URL = 'https://ai.allio.agency/img/';

    public function __invoke(): array
    {
        $intValue = new IntValueExtractor();
        $floatValue = new FloatValueExtractor();
        $strValue = new StringValueExtractor();

        return [
            Section::class => [
                Section::REF_BUILDING => fn(MapperDto $dto) => $this->getBuildingExternalId($dto),
                'external_id' => fn(MapperDto $dto) => $this->getSectionExternalId($dto),
                'title' => fn(MapperDto $dto) => $this->getSectionTitle($dto),
            ],
            Floor::class => [
                Floor::REF_BUILDING => fn(MapperDto $dto) => $this->getBuildingExternalId($dto),
                'external_id' => fn(MapperDto $dto) => $this->getFloorExternalId($dto),
                'title' => fn(MapperDto $dto) => $this->getFloorTitle($dto),
                'number' => fn(MapperDto $dto) => $intValue($dto->source['floor'] ?? 1)
            ],
            PremisesType::class => [
                PremisesType::REF_GROUP => fn(MapperDto $dto) => $this->getTypeGroupExternalId($dto),
                'external_id' => fn(MapperDto $dto) => $this->getTypeExternalId($dto),
                'title' => fn(MapperDto $dto) => $strValue(Arr::get($dto->source, 'size.name', '')),
                'short_title' => fn(MapperDto $dto) => $this->getTypeShortTitle($dto),
                'rooms' => fn(MapperDto $dto) => $this->getRoomsCnt($dto),
                'slug' => fn(MapperDto $dto) => Str::slug($dto->result['title'])
            ],
            PremisesProxy::class => [
                PremisesProxy::REF_FLOOR => fn(MapperDto $dto) => $this->getFloorExternalId($dto),
                PremisesProxy::REF_SECTION => fn(MapperDto $dto) => $this->getSectionExternalId($dto),
                PremisesProxy::REF_TYPE => fn(MapperDto $dto) => $this->getTypeExternalId($dto),
                PremisesProxy::REF_STATUS => fn(MapperDto $dto) => $this->replaceExternalId(
                    $strValue($dto->source['state'] ?? '')
                ),

                'external_id' => fn(MapperDto $dto) => $this->replaceExternalId(
                    $intValue($dto->source['id'] ?? 0)
                ),
                'title' => fn(MapperDto $dto) => $this->getTitle($dto),
                'number' => 'number',
                'rooms' => fn(MapperDto $dto) => $this->getRoomsCnt($dto),
                'area_total' => fn(MapperDto $dto) => $floatValue($dto->source['square'] ?? 0),
                'area_living' => fn(MapperDto $dto) => $floatValue($dto->source['living_square'] ?? 0),
                'area_kitchen' => fn(MapperDto $dto) => $this->getAreaKitchen($dto),
                'price_total' => fn(MapperDto $dto) => $floatValue(Arr::get($dto->source, 'price.base', 0.0)),
                'price_sale' => fn(MapperDto $dto) => $floatValue(Arr::get($dto->source, 'price.promo', 0.0)),
                'price_meter' => fn(MapperDto $dto) => $floatValue(Arr::get($dto->source, 'price.base_m2', 0.0)),

                PremisesProxy::REF_IMAGE_PLAN => fn(MapperDto $dto) => $this->getFilePathAndHash($dto),
                PremisesProxy::REF_FEATURES => fn(MapperDto $dto) => $this->getFeatures($dto),
                'hash' => fn(MapperDto $obj) => $this->getPremisesHash($obj)
            ]
        ];
    }

    private function getBuildingExternalId(MapperDto $dto): int
    {
        return $this->replaceExternalId(
            (new IntValueExtractor())($dto->source['building_id'] ?? 0)
        );
    }

    private function getFloorExternalId(MapperDto $dto): string
    {
        return $this->replaceExternalId(
            $this->getBuildingExternalId($dto) . '__' . $this->getFloorTitle($dto)
        );
    }

    private function getFloorTitle(MapperDto $dto): int
    {
        return (new IntValueExtractor())($dto->source['floor'] ?? 0);
    }

    private function getSectionExternalId(MapperDto $dto): string
    {
        return $this->replaceExternalId(
            $this->getBuildingExternalId($dto) . '__' . $this-> getSectionTitle($dto)
        );
    }

    private function getSectionTitle(MapperDto $dto): int
    {
        return (new IntValueExtractor())($dto->source['porch'] ?? 0);
    }

    private function getTypeGroupExternalId(MapperDto $dto): string
    {
        return $this->replaceExternalId(
            (new StringValueExtractor())($dto->source['stype'] ?? '')
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
                    (new StringValueExtractor())(Arr::get($dto->source, 'size.type')),
                    $intValue(Arr::get($dto->source, 'size.rooms')),
                    $intValue(Arr::get($dto->source, 'size.is_studio'))
                ]
            )
        );
    }

    private function getAreaKitchen(MapperDto $dto): float
    {
        $areas = Arr::get($dto->source, 'area', []);

        if (!$areas) {
            return 0.0;
        }

        foreach ($areas as $area) {
            if (in_array(Arr::get($area, 'type'), ['KITCHEN', 'KITCHEN_NICHE'], true)) {
                return (new FloatValueExtractor())(Arr::get($area, 'square', 0.0));
            }
        }

        return 0.0;
    }

    private function getTitle(MapperDto $dto): string
    {
        $strValue = new StringValueExtractor();
        $res = $strValue(Arr::get($dto->source, 'size.name'));
        $number = $strValue($dto->source['number'] ?? '');

        if (mb_strlen($number)) {
            $res .= ' №' . $number;
        }

        return $res;
    }

    private function getTypeShortTitle(MapperDto $dto): string
    {
        $strValue = new StringValueExtractor();

        if ($strValue($dto->source['stype'] ?? '') !== 'APARTMENT') {
            return $dto->result['title'];
        }

        $intValue = new IntValueExtractor();

        if ($intValue(Arr::get($dto->source, 'size.is_studio'))) {
            return trans('kelnik-estate-import::allio.type.short.studio');
        }

        return trans(
            'kelnik-estate-import::allio.type.short.rooms',
            [
                'cnt' => $intValue(Arr::get($dto->source, 'size.rooms'))
            ]
        );
    }

    private function getRoomsCnt(MapperDto $dto): int
    {
        $intValue = new IntValueExtractor();

        return $intValue(Arr::get($dto->source, 'size.is_studio', 0))
            ? 0
            : $intValue(Arr::get($dto->source, 'size.rooms', 0));
    }

    private function getFilePathAndHash(MapperDto $dto): ?array
    {
        $plan = (function () use ($dto) {
            $images = (new ArrayValueExtractor())(Arr::get($dto->source, 'images', []));
            foreach ($images as $image) {
                if (Arr::get($image, 'type') === 'GENERAL') {
                    return Arr::get($image, 'path');
                }
            }

            return null;
        })();

        return $plan
            ? resolve(
                DownloadService::class,
                [
                    'logger' => $dto->logger,
                    'storage' => $dto->storage,
                    'dirPath' => $dto->filesDirPath
                ]
            )->download(self::IMG_BASE_URL . ltrim($plan, '/'))
            : null;
    }

    private function getFeatures(MapperDto $dto): array
    {
        return array_map(
            fn($el) => [
                'external_id' => $this->replaceExternalId(
                    (new StringValueExtractor())(Arr::get($el, 'type'))
                )
            ],
            (new ArrayValueExtractor())(Arr::get($dto->source, 'features', []))
        );
    }
}
