<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateVisual\Http\Handlers\Contracts\StepHandler;
use Kelnik\EstateVisual\Models\Filters\BuildingBase;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleRepository;

final class Building extends StepHandler
{
    public function handle(array $request): Collection
    {
        $id = (int)($request['buildingId'] ?? $request['firstStepId'] ?? 0);

        if (!$id) {
            return new Collection([
                'step' => new StepElement()
            ]);
        }

        $cacheId = $this->getCacheId(
            'stepBuilding',
            [$this->selector->getKey(), $id, $this->getDataFilter($request)]
        );
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $step = $this->stepElementRepository->findByPrimaryWithAngles($this->selector->getKey(), $id);

        // TODO: check the mask type of all elements to collect the necessary statistics
        $stat = [
            Factory::STEP_SECTION => $this->getStat(Factory::STEP_SECTION, $request, $step->estate_model_id),
            Factory::STEP_FLOOR => $this->getStat(Factory::STEP_FLOOR, $request, $step->estate_model_id)
        ];
        $renders = [];

        $step->angles->map(static function (StepElementAngle $angle) use ($stat, &$renders) {
            $angle->masks->map(static function (StepElementAngleMask $mask) use ($stat, &$renders) {
                $mask->premisesStat = $stat[$mask->type->value][$mask->element->estate_model_id]['types'] ?? [];

                if ($mask->type->isStep()) {
                    $renders[$mask->element_id] = $mask->element_id;
                }

                return $mask;
            });

            return $angle;
        });
        unset($stat);

        $res = new Collection([
            'breadcrumbs' => $this->buildBreadCrumbs($step),
            'step' => $step,
            'filters' => $this->getForm($request),
            'elementsRender' => resolve(StepElementAngleRepository::class)->getElementsRender($renders)
        ]);

        Cache::tags([
            $this->estateService->getModuleCacheTag(),
            $this->selectorService->getCacheTag($this->selector)
        ])->put($cacheId, $res, self::CACHE_TTL);

        return $res;
    }

    private function getStat(string $stepName, array $request, int|string $buildingId): Collection
    {
        $buildingFilter = new BuildingBase();
        $buildingFilter->setRequestValues([$buildingFilter->getName() => [$buildingId]]);
        $this->addFilter($buildingFilter);
        unset($buildingFilter);

        $dataFilter = $this->getDataFilter($request);

        $repoMethod = 'getFloorIdsWithPremisesStat';
        $fieldKey = 'floor_id';
        $relationName = 'floor';

        if ($stepName === Factory::STEP_SECTION) {
            $repoMethod = 'getSectionIdsWithPremisesStat';
            $fieldKey = 'section_id';
            $relationName = 'section';
        }

        $data = $this->searchRepository->{$repoMethod}($dataFilter);
        $res = [];

        /** @var EstateModel $el */
        foreach ($data as $el) {
            $attrValue = $el->getAttribute($fieldKey);
            $res[$attrValue] ??= [
                'id' => $attrValue,
                'building_id' => $el->{$relationName}->building_id,
                'types' => []
            ];

            $type = &$res[$attrValue]['types'][$el->type_id];

            $res[$attrValue]['types'][$el->type_id] = [
                'title' => $el->type->title,
                'short_title' => $el->type->short_title,
                'rooms' => $el->type->rooms,
                'cnt' => ($type['cnt'] ?? 0) + $el->cnt,
                'price_min' => min($type['price_min'] ?? $el->price_min, $el->price_min)
            ];
        }

        return new Collection($res);
    }
}
