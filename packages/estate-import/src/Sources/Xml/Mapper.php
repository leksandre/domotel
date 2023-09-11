<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Xml;

use Illuminate\Support\Str;
use Kelnik\EstateImport\Models\Proxy\Building;
use Kelnik\EstateImport\Models\Proxy\Complex;
use Kelnik\EstateImport\Models\Proxy\Contracts\EstateModelProxy;
use Kelnik\EstateImport\Models\Proxy\Floor;
use Kelnik\EstateImport\Models\Proxy\Premises;
use Kelnik\EstateImport\Models\Proxy\PremisesStatus;
use Kelnik\EstateImport\Models\Proxy\PremisesType;
use Kelnik\EstateImport\Models\Proxy\PremisesTypeGroup;
use Kelnik\EstateImport\Models\Proxy\Section;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper as AbstractMapper;
use Kelnik\EstateImport\PreProcessor\Contracts\MapperDto;
use Kelnik\EstateImport\ValueExtractors\FloatValueExtractor;
use Kelnik\EstateImport\ValueExtractors\IntValueExtractor;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;
use SimpleXMLElement;

final class Mapper extends AbstractMapper
{
    private const PREMISES_EXTERNAL_ID = 'ID';
    private const PREMISES_TITLE = 'Num';
    private const PREMISES_NUMBER = 'NumberOnFloor';
    private const PREMISES_AREA_TOTAL = 'SquareTotal';
    private const PREMISES_AREA_LIVING = 'SquareLive';
    private const PREMISES_ROOM_COUNT = 'RoomsCount';
    private const PREMISES_STATE = 'StateBase';
    private const PREMISES_PRICE = 'Price';
    private const PREMISES_PRICE_TOTAL = 'PriceTotal';
    private const BUILDING_NAME = 'Housing';
    private const BUILDING_ID = 'BuildObjectID';
    private const SECTION_NAME = 'NumberOfSection';
    private const SECTION_FLOORS_COUNT = 'NumberOfFloorsSection';
    private const FLOOR_NAME = 'Floor';
    private const PREMISES_TYPE_GROUP = 'FlatType';
    private const PREMISES_TYPE_GROUP_ID = 'FlatTypeID';
    private const PREMISES_TYPE = 'RoomsCount';
    private const PREMISES_DESIGN = 'Design';
    private const PREMISES_DESIGN_STUDIO = 'студия';
    private const PREMISES_DESIGN_STANDARD = 'стандарт';
    private const PREMISES_STUDIO_ROOMS = 1;
    private const COMPLEX_EXTERNAL_ID = 'complex';

    public function __invoke(): array
    {
        $flatMap = [
            Complex::class => [
                'title' => self::BUILDING_NAME,
                'external_id' => fn(MapperDto $obj) => $this->getComplexExternalId($obj->source),
            ],
            Building::class => [
                Building::REF_COMPLEX => fn(MapperDto $obj) => $this->getComplexExternalId($obj->source),
                'title' => self::BUILDING_NAME,
                'external_id' => fn(MapperDto $obj) => $this->getBuildingExternalId($obj->source),
                'hash' => fn(MapperDto $obj) => $this->getHash($obj->result)
            ],
            Section::class => [
                Section::REF_BUILDING => fn(MapperDto $obj) => $this->getBuildingExternalId($obj->source),
                'title' => self::SECTION_NAME,
                'floor_max' => ['name' => self::SECTION_FLOORS_COUNT, 'extractor' => IntValueExtractor::class],
                'external_id' => fn(MapperDto $obj) => $this->getSectionExternalId($obj->source),
                'hash' => fn(MapperDto $obj) => $this->getHash($obj->result)
            ],
            Floor::class => [
                Floor::REF_BUILDING => fn(MapperDto $obj) => $this->getBuildingExternalId($obj->source),
                'title' => self::FLOOR_NAME,
                'number' => fn(MapperDto $obj) => $this->getFloorNumber($obj->source),
                'external_id' => fn(MapperDto $obj) => $this->getFloorExternalId($obj->source),
                'hash' => fn(MapperDto $obj) => $this->getHash($obj->result)
            ],
            PremisesStatus::class => [
                'title' => self::PREMISES_STATE,
                'external_id' => fn(MapperDto $obj) => $this->getPremisesStatusExternalId($obj->source)
            ],
            PremisesTypeGroup::class => [
                'title' => self::PREMISES_TYPE_GROUP,
                'slug' => fn(MapperDto $obj) => $this->getPremisesTypeGroupSlug($obj->result),
                'external_id' => fn(MapperDto $obj) => $this->getPremisesTypeGroupExternalId($obj->source)
            ],
            PremisesType::class => [
                'title' => fn(MapperDto $obj) => $this->getPremisesTypeTitle($obj->source),
                'slug' => fn(MapperDto $obj) => $this->getPremisesTypeSlug($obj->result),
                PremisesType::REF_GROUP => fn(MapperDto $obj) => $this->getPremisesTypeGroupExternalId($obj->source),
                'rooms' => ['name' => self::PREMISES_ROOM_COUNT, 'extractor' => IntValueExtractor::class],
                'short_title' => fn(MapperDto $obj) => $this->getPremisesTypeTitle($obj->source),
                'external_id' => fn(MapperDto $obj) => $this->getPremisesTypeExternalId($obj->source)
            ],
            Premises::class => [
                Premises::REF_FLOOR => fn(MapperDto $obj) => $this->getFloorExternalId($obj->source),
                Premises::REF_SECTION => fn(MapperDto $obj) => $this->getSectionExternalId($obj->source),
                Premises::REF_TYPE => fn(MapperDto $obj) => $this->getPremisesTypeExternalId($obj->source),
                Premises::REF_STATUS => fn(MapperDto $obj) => $this->getPremisesStatusExternalId($obj->source),
                'external_id' => self::PREMISES_EXTERNAL_ID,
                'title' => self::PREMISES_TITLE,
                'number' => self::PREMISES_TITLE,
                'number_on_floor' => ['name' => self::PREMISES_NUMBER, 'extractor' => IntValueExtractor::class],
                'area_total' => ['name' => self::PREMISES_AREA_TOTAL, 'extractor' => FloatValueExtractor::class],
                'area_living' => ['name' => self::PREMISES_AREA_LIVING, 'extractor' => FloatValueExtractor::class],
                'rooms' => ['name' => self::PREMISES_ROOM_COUNT, 'extractor' => IntValueExtractor::class],
                'price' => ['name' => self::PREMISES_PRICE, 'extractor' => FloatValueExtractor::class],
                'price_total' => ['name' => self::PREMISES_PRICE_TOTAL, 'extractor' => FloatValueExtractor::class],
                Premises::REF_FEATURES => fn(MapperDto $obj) => $this->prepareFeatures($obj->source),
                'hash' => fn(MapperDto $obj) => $this->getHash($obj->result)
            ]
        ];

        /**
         * @var EstateModelProxy $a
         * @var EstateModelProxy $b
         */
        uksort($flatMap, static fn($a, $b) => $a::getSort() <=> $b::getSort());

        return [
            'Flat' => $flatMap
        ];
    }

    private function getComplexExternalId(SimpleXMLElement $el): string
    {
        return self::COMPLEX_EXTERNAL_ID;
    }

    private function getBuildingExternalId(SimpleXMLElement $el): string
    {
        return $this->replaceExternalId((new StringValueExtractor())($el->{self::BUILDING_ID}));
    }

    private function getSectionExternalId(SimpleXMLElement $el): string
    {
        return $this->replaceExternalId(
            $this->getBuildingExternalId($el) .
            '_s' .
            mb_strtolower((new StringValueExtractor())($el->{self::SECTION_NAME}))
        );
    }

    private function getFloorExternalId(SimpleXMLElement $el): string
    {
        return $this->replaceExternalId(
            $this->getBuildingExternalId($el) .
            '_f' .
            mb_strtolower((new StringValueExtractor())($el->{self::FLOOR_NAME}))
        );
    }

    private function getFloorNumber(SimpleXMLElement $el): int
    {
        return (new IntValueExtractor())($el->{self::FLOOR_NAME});
    }

    private function getPremisesStatusExternalId(SimpleXMLElement $el): int|string
    {
        return $this->replaceExternalId(
            mb_strtolower((new StringValueExtractor())($el->{self::PREMISES_STATE}))
        );
    }

    private function getPremisesTypeGroupExternalId(SimpleXMLElement $el): string
    {
        return $this->replaceExternalId(
            (new StringValueExtractor())($el->{self::PREMISES_TYPE_GROUP_ID})
        );
    }

    private function getPremisesTypeExternalId(SimpleXMLElement $el): string
    {
        $res = [
            $this->getPremisesTypeGroupExternalId($el),
            (new StringValueExtractor())($el->{self::PREMISES_TYPE})
        ];

        $design = (new StringValueExtractor())($el->{self::PREMISES_DESIGN});
        $isStudio = $this->isStudio($el);
        $isStandard = $this->isStandard($el);

        if ($isStudio) {
            $res[1] = 0;
        }

        if ($design && !$isStudio && !$isStandard) {
            $res[] = $design;
        }

        return $this->replaceExternalId(implode('-', $res));
    }

    private function getPremisesTypeTitle(SimpleXMLElement $el): string
    {
        $rooms = (new StringValueExtractor())($el->{self::PREMISES_TYPE});
        $design = (new StringValueExtractor())($el->{self::PREMISES_DESIGN});
        $res = $rooms;

        if ($this->isStudio($el)) {
            return $design;
        }

        if ((int)$rooms && !$this->isStandard($el)) {
            $res .= ' ' . $design;
        }

        return $res;
    }

    private function getPremisesTypeGroupSlug(array $res): ?string
    {
        return Str::slug($res['title'] . '-' . Str::random(4) ?? null);
    }

    private function getPremisesTypeSlug(array $res): ?string
    {
        return Str::slug($res['title'] . '-' . Str::random(4) ?? null);
    }

    private function prepareFeatures(SimpleXMLElement $el): array
    {
        $res = [];

        foreach (['Finishing', 'Design'] as $fieldName) {
            $val = (new StringValueExtractor())($el->{$fieldName});

            if (mb_strlen($val)) {
                $res[] = $val;
            }
        }

        return $res;
    }

    private function isStudio(SimpleXMLElement $el): bool
    {
        $rooms = (new IntValueExtractor())($el->{self::PREMISES_TYPE});
        $design = (new StringValueExtractor())($el->{self::PREMISES_DESIGN});

        return $rooms === self::PREMISES_STUDIO_ROOMS && mb_strtolower($design) === self::PREMISES_DESIGN_STUDIO;
    }

    private function isStandard(SimpleXMLElement $el): bool
    {
        $rooms = (new IntValueExtractor())($el->{self::PREMISES_TYPE});
        $design = (new StringValueExtractor())($el->{self::PREMISES_DESIGN});

        return $rooms > 0 && mb_strtolower($design) === self::PREMISES_DESIGN_STANDARD;
    }
}
