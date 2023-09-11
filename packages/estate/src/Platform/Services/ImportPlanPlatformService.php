<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Repositories\Contracts\BuildingRepository;
use Kelnik\Estate\Repositories\Contracts\FloorRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Kelnik\Estate\Repositories\Contracts\SectionRepository;
use League\Flysystem\FilesystemException;
use Orchid\Attachment\File;

final class ImportPlanPlatformService implements Contracts\ImportPlanPlatformService
{
    public function __construct()
    {
    }

    public function getBuildingsByComplex(int|string $complexPrimary): array
    {
        return resolve(BuildingRepository::class)
            ->getAllByComplex($complexPrimary)
            ->map(static fn(Building $building) => [
                'id' => $building->getKey(),
                'title' => '[' . $building->getKey() . '] ' . $building->title
            ])
            ->toArray();
    }

    public function getSectionsByBuilding(int|string $buildingPrimary): array
    {
        return resolve(SectionRepository::class)
            ->getAllByBuilding($buildingPrimary)
            ->sortBy(static fn(Section $section) => $section->title, SORT_NATURAL)
            ->map(static fn(Section $section) => [
                'id' => $section->getKey(),
                'title' => '[' . $section->getKey() . '] ' . $section->title
            ])
            ->values()
            ->toArray();
    }

    public function getFloorsByBuilding(int|string $buildingPrimary): array
    {
        return resolve(FloorRepository::class)
            ->getAllByBuilding($buildingPrimary)
            ->sortBy(static fn(Floor $floor) => $floor->title, SORT_NATURAL)
            ->map(static fn(Floor $floor) => [
                'id' => $floor->getKey(),
                'title' => '[' . $floor->getKey() . '] ' . $floor->title
            ])
            ->values()
            ->toArray();
    }

    public function getFloorsBySection(int|string $sectionPrimary): array
    {
        return resolve(FloorRepository::class)
            ->getAllBySection($sectionPrimary)
            ->sortBy(static fn(Floor $floor) => $floor->title, SORT_NATURAL)
            ->map(fn(Floor $floor) => [
                'id' => $floor->getKey(),
                'title' => '[' . $floor->getKey() . '] ' . $floor->title
            ])
            ->values()
            ->toArray();
    }

    public function import(string $type, string $field, int|string $section, array $floors, UploadedFile $file): int
    {
        /** @var PremisesRepository $repository */
        $repository = resolve(PremisesRepository::class);
        $premises = $repository->getByFloorAndSection($floors, $section);

        if ($premises->isEmpty()) {
            return 0;
        }

        $numbers = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $numbers = explode(',', $numbers);
        $searchColumn = 'number';

        if ($type === self::TYPE_NUMBER_ON_FLOOR) {
            $searchColumn = 'number_on_floor';
            $numbers = array_map('intval', $numbers);
        }

        $cnt = 0;

        /** @var Premises $el */
        foreach ($premises as $el) {
            if (in_array($el->getAttribute($searchColumn), $numbers, true)) {
                try {
                    $attachment = (new File(
                        $file,
                        config('kelnik-estate.storage.disk'),
                        EstateServiceProvider::MODULE_NAME
                    ))->load();
                    $el->setAttribute($field, $attachment->getKey());
                    $repository->save($el);
                    $cnt++;
                } catch (FilesystemException $e) {
                    Log::error('Upload file error', ['msg' => $e->getMessage()]);
                }
            }
        }

        return $cnt;
    }
}
