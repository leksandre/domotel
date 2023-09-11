<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\UploadedFile;

interface ImportPlanPlatformService
{
    public const TYPE_NUMBER = 'number';
    public const TYPE_NUMBER_ON_FLOOR = 'numberOnFloor';

    public function getBuildingsByComplex(int|string $complexPrimary): array;

    public function getSectionsByBuilding(int|string $buildingPrimary): array;

    public function getFloorsByBuilding(int|string $buildingPrimary): array;

    public function getFloorsBySection(int|string $sectionPrimary): array;

    public function import(string $type, string $field, int|string $section, array $floors, UploadedFile $file): int;
}
