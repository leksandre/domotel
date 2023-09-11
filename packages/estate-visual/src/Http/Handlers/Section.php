<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateVisual\Http\Handlers\Contracts\StepHandler;
use Kelnik\EstateVisual\Models\Filters\SectionBase;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleRepository;

final class Section extends StepHandler
{
    public function handle(array $request): Collection
    {
        $id = (int)($request['sectionId'] ?? $request['firstStepId'] ?? 0);

        if (!$id) {
            return new Collection([
                'step' => new StepElement()
            ]);
        }

        $cacheId = $this->getCacheId(
            'stepSection',
            [$this->selector->getKey(), $id, $this->getDataFilter($request)]
        );
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $step = $this->stepElementRepository->findByPrimaryWithAngles($this->selector->getKey(), $id);
        $stat = $this->getStat($request, $step->estate_model_id);
        $renders = [];

        $step->angles->map(static function (StepElementAngle $angle) use ($stat, &$renders) {
            $angle->masks->map(static function (StepElementAngleMask $mask) use ($stat, &$renders) {
                $mask->premisesStat = $stat[$mask->element->estate_model_id]['types'] ?? [];

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

    private function getStat(array $request, int|string $sectionId): Collection
    {
        $sectionFilter = new SectionBase();
        $sectionFilter->setRequestValues([$sectionFilter->getName() => [$sectionId]]);
        $this->addFilter($sectionFilter);
        unset($sectionFilter);

        $dataFilter = $this->getDataFilter($request);

        $data = $this->searchRepository->getFloorIdsWithPremisesStat($dataFilter);
        $res = [];

        /** @var EstateModel $el */
        foreach ($data as $el) {
            $floorId = $el->getAttribute('floor_id');

            $res[$floorId] ??= [
                'id' => $floorId,
                'section_id' => $el->section_id,
                'types' => []
            ];

            $type = &$res[$floorId]['types'][$el->type_id];

            $res[$floorId]['types'][$el->type_id] = [
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
