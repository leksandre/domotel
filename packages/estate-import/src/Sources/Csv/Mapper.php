<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Csv;

use DateTimeInterface;
use Kelnik\Core\Helpers\DateHelper;
use Kelnik\EstateImport\Models\Proxy\Building;
use Kelnik\EstateImport\Models\Proxy\Completion;
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
use Kelnik\EstateImport\Services\Contracts\DownloadService;
use Kelnik\EstateImport\ValueExtractors\ArrayValueExtractor;
use Kelnik\EstateImport\ValueExtractors\IntValueExtractor;

final class Mapper extends AbstractMapper
{
    private const PREMISES_EXTERNAL_ID = 0;
    private const PREMISES_NUMBER = 1;
    private const PREMISES_AREA_TOTAL = 2;
    private const PREMISES_AREA_LIVING = 3;
    private const PREMISES_AREA_KITCHEN = 4;
    private const PREMISES_ROOM_COUNT = 5;
    private const PREMISES_STATE = 6;
    private const PREMISES_PRICE_TOTAL = 7;
    private const COMPLEX_NAME = 8;
    private const BUILDING_NAME = 9;
    private const SECTION_NAME = 10;
    private const FLOOR_NAME = 11;
    private const PREMISES_TYPE_GROUP = 12;
    private const PREMISES_TYPE = 13;
    private const COMPLETION_DATE = 14;
    private const PREMISES_PLAN_IMAGE = 15;
    private const PREMISES_FEATURES = 16;
    private const PLANOPLAN_CODE = 17;

    public function __invoke(): array
    {
        $map = [
            Complex::class => [
                'title' => self::COMPLEX_NAME,
                'external_id' => fn(MapperDto $obj) => $this->getComplexExternalId($obj->source),
                'hash' => fn(MapperDto $obj) => $this->getHash($obj->result)
            ],
            Completion::class => [
                'title' => fn(MapperDto $obj) => $this->getCompletionTitle($obj->source),
                'event_date' => self::COMPLETION_DATE,
                'external_id' => fn(MapperDto $obj) => $this->getCompletionExternalId($obj->source)
            ],
            Building::class => [
                Building::REF_COMPLEX => fn(MapperDto $obj) => $this->getComplexExternalId($obj->source),
                Building::REF_COMPLETION => fn(MapperDto $obj) => $this->getCompletionExternalId($obj->source),
                'title' => self::BUILDING_NAME,
                'external_id' => fn(MapperDto $obj) => $this->getBuildingExternalId($obj->source),
                'hash' => fn(MapperDto $obj) => $this->getHash($obj->result)
            ],
            Section::class => [
                Section::REF_BUILDING => fn(MapperDto $obj) => $this->getBuildingExternalId($obj->source),
                'title' => fn(MapperDto $obj) => $this->getSectionTitle($obj->source),
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
                'external_id' => fn(MapperDto $obj) => $this->getPremisesTypeGroupExternalId($obj->source)
            ],
            PremisesType::class => [
                'title' => self::PREMISES_TYPE,
                PremisesType::REF_GROUP => fn(MapperDto $obj) => $this->getPremisesTypeGroupExternalId($obj->source),
                'external_id' => fn(MapperDto $obj) => $this->getPremisesTypeExternalId($obj->source)
            ],
            Premises::class => [
                Premises::REF_FLOOR => fn(MapperDto $obj) => $this->getFloorExternalId($obj->source),
                Premises::REF_SECTION => fn(MapperDto $obj) => $this->getSectionExternalId($obj->source),
                Premises::REF_TYPE => fn(MapperDto $obj) => $this->getPremisesTypeExternalId($obj->source),
                Premises::REF_STATUS => fn(MapperDto $obj) => $this->getPremisesStatusExternalId($obj->source),
                'external_id' => self::PREMISES_EXTERNAL_ID,
                'title' => self::PREMISES_NUMBER,
                'number' => self::PREMISES_NUMBER,
                'area_total' => self::PREMISES_AREA_TOTAL,
                'area_living' => self::PREMISES_AREA_LIVING,
                'area_kitchen' => self::PREMISES_AREA_KITCHEN,
                'rooms' => self::PREMISES_ROOM_COUNT,
                'price_total' => self::PREMISES_PRICE_TOTAL,
                'planoplan_code' => self::PLANOPLAN_CODE,
                Premises::REF_IMAGE_PLAN => fn(MapperDto $obj) => $this->getFilePathAndHash($obj),
                Premises::REF_FEATURES => fn(MapperDto $obj) => $this->prepareFeatures($obj->source),
                'hash' => fn(MapperDto $obj) => $this->getPremisesHash($obj)
            ]
        ];

        /**
         * @var EstateModelProxy $a
         * @var EstateModelProxy $b
         */
        uksort($map, static fn($a, $b) => $a::getSort() <=> $b::getSort());

        return $map;
    }

    private function getComplexExternalId(array $row): string
    {
        return $this->replaceExternalId(mb_strtolower($row[self::COMPLEX_NAME]));
    }

    private function getCompletionTitle(array $row): string
    {
        /** @var ?DateTimeInterface $val */
        $val = $row[self::COMPLETION_DATE] ?? null;

        if (!$val instanceof DateTimeInterface) {
            return '';
        }

        return trans(
            'kelnik-estate::factory.dateQuarter',
            [
                'quarter' => DateHelper::quarter($val),
                'year' => $val->format('Y')
            ]
        );
    }

    private function getCompletionExternalId(array $row): ?string
    {
        /** @var ?DateTimeInterface $val */
        $val = $row[self::COMPLETION_DATE] ?? null;

        if (!$val instanceof DateTimeInterface) {
            return null;
        }

        return $this->replaceExternalId($val->format('Y-m-d'));
    }

    private function getBuildingTitle(array $row): string
    {
        return  mb_strtolower($row[self::BUILDING_NAME]);
    }

    private function getBuildingExternalId(array $row): string
    {
        $title = $this->getBuildingTitle($row);

        return mb_strlen($title)
            ? $this->replaceExternalId($this->getComplexExternalId($row) . '_' . $title)
            : '';
    }

    private function getSectionTitle(array $row): string
    {
        return $row[self::SECTION_NAME];
    }

    private function getSectionExternalId(array $row): string
    {
        $title = mb_strtolower($this->getSectionTitle($row));

        return mb_strlen($title)
            ? $this->replaceExternalId($this->getBuildingExternalId($row) . '_' . $title)
            : '';
    }

    private function getFloorExternalId(array $row): string
    {
        return $this->replaceExternalId(
            $this->getBuildingExternalId($row) . '_' . mb_strtolower($row[self::FLOOR_NAME])
        );
    }

    private function getFloorNumber(array $row): int
    {
        return (new IntValueExtractor())($row[self::FLOOR_NAME]);
    }

    private function getPremisesStatusExternalId(array $row): int|string
    {
        return $this->replaceExternalId($row[self::PREMISES_STATE]);
    }

    private function getPremisesTypeGroupExternalId(array $row): string
    {
        return $this->replaceExternalId(mb_strtolower($row[self::PREMISES_TYPE_GROUP]));
    }

    private function getPremisesTypeExternalId(array $row): string
    {
        return $this->replaceExternalId(
            mb_strtolower($row[self::PREMISES_TYPE_GROUP] . '-' . $row[self::PREMISES_TYPE])
        );
    }

    private function getFilePathAndHash(MapperDto $obj): ?array
    {
        return $obj->source[self::PREMISES_PLAN_IMAGE]
            ? resolve(
                DownloadService::class,
                ['logger' => $obj->logger, 'storage' => $obj->storage, 'dirPath' => $obj->filesDirPath]
            )->download($obj->source[self::PREMISES_PLAN_IMAGE])
            : null;
    }

    private function prepareFeatures(array $row): array
    {
        return array_filter(
            (new ArrayValueExtractor())(explode(',', $row[self::PREMISES_FEATURES]), 'trim'),
            static fn($el) => $el !== ''
        );
    }
}
