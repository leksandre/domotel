<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateVisual\Http\Handlers\Contracts\StepHandler;
use Kelnik\EstateVisual\Models\Filters\ComplexBase;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\CompletionRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleRepository;

final class Complex extends StepHandler
{
    public function handle(array $request): Collection
    {
        $cacheId = $this->getCacheId('stepComplex', [$this->selector->getKey(), $this->getDataFilter($request)]);
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $step = $this->stepElementRepository->getFirstWithAngles($this->selector->getKey());
        // TODO: check the mask type of all elements to collect the necessary statistics
        $stat = [
            Factory::STEP_BUILDING => $this->getStat(Factory::STEP_BUILDING, $request, $step->estate_model_id),
            Factory::STEP_SECTION => $this->getStat(Factory::STEP_SECTION, $request, $step->estate_model_id),
            Factory::STEP_FLOOR => $this->getStat(Factory::STEP_FLOOR, $request, $step->estate_model_id)
        ];
        $renders = [];

        $step->angles->map(static function (StepElementAngle $angle) use ($stat, &$renders) {
            $angle->masks->map(static function (StepElementAngleMask $mask) use ($stat, &$renders) {
                $mask->premisesStat = $stat[$mask->type->value][$mask->element->estate_model_id]['types'] ?? [];
                $mask->completion = $stat[$mask->type->value][$mask->element->estate_model_id]['completion'] ?? null;

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

    private function getStat(string $stepName, array $request, int|string $complexId): Collection
    {
        $complexFilter = new ComplexBase();
        $complexFilter->setRequestValues([$complexFilter->getName() => [$complexId]]);
        $this->addFilter($complexFilter);
        unset($complexFilter);

        $dataFilter = $this->getDataFilter($request);

        $repoMethod = 'getFloorIdsWithPremisesStat';
        $fieldKey = 'floor_id';
        $relationName = 'floor';

        if ($stepName === Factory::STEP_BUILDING) {
            $repoMethod = 'getBuildingIdsWithPremisesStat';
            $fieldKey = 'buildingId';
            $relationName = null;
        } elseif ($stepName === Factory::STEP_SECTION) {
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
                'building_id' => $relationName ? $el->{$relationName}->building_id : $el->getAttribute($fieldKey),
                'types' => [],
                'completion' => new Completion()
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

        if ($stepName === Factory::STEP_BUILDING && $res) {
            resolve(CompletionRepository::class)->getByBuildingPrimaryKeys(array_keys($res))
                ->each(static function (Completion $completion) use (&$res) {
                    foreach ($completion->buildingIds as $bId) {
                        $res[$bId]['completion'] = $completion;
                    }
                });
        }

        return new Collection($res);
    }
}
