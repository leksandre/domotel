<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Services;

use Kelnik\Core\Helpers\ImageHelper;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleMaskRepository;

final class VisualService implements Contracts\VisualService
{
    public function __construct(private StepElementAngleMaskRepository $stepElementAngleMaskRepository)
    {
    }

    public function getPremisesOnFloorPlan(int|string $primaryKey): array
    {
        $mask = $this->stepElementAngleMaskRepository->getPremisesOnFloorPlan($primaryKey);
        $res = [];

        if (!$mask->exists || !$mask->relationLoaded('angle') || !$mask->angle->relationLoaded('render')) {
            return $res;
        }

        $res['coords'] = $mask->coords;
        $res['render'] = $mask->angle->render->url();
        $res['sizes'] = ImageHelper::getImageSizes($mask->angle->render);

        return $res;
    }

    public function getAssets(): array
    {
        $assets = config('kelnik-estate-visual.assets');

        if (!$assets) {
            return [];
        }

        $excludeFolders = ['.', '..'];

        foreach ($assets as $section => $sectData) {
            if (!$sectData['path'] || !file_exists($sectData['path'])) {
                continue;
            }

            $files = array_diff(scandir($sectData['path']), $excludeFolders);

            if (!$files) {
                continue;
            }

            foreach ($files as $fileName) {
                if (mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) === 'map') {
                    continue;
                }
                $res[$section][] = $sectData['url'] . '/' . $fileName;
            }
        }

        return $res;
    }
}
