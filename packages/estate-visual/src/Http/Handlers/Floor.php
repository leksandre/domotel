<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\EstateVisual\Http\Handlers\Contracts\StepHandler;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractFilter;
use Kelnik\EstateVisual\Models\Filters\FloorBase;
use Kelnik\EstateVisual\Models\Filters\SectionBase;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\Page\Services\Contracts\PageLinkService;

final class Floor extends StepHandler
{
    public function handle(array $request): Collection
    {
        $id = (int)($request['floorId'] ?? $request['firstStepId'] ?? 0);

        if (!$id) {
            return new Collection([
                'step' => new StepElement()
            ]);
        }

        $cacheId = $this->getCacheId('stepFloor', [$this->selector->getKey(), $id, $this->getDataFilter($request)]);
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $res = new Collection();

        /**
         * @var ?StepElement $stepEl
         * @var ?StepElement $parentStepEl
         */
        $stepEl = $this->stepElementRepository->findByPrimaryWithAngles($this->selector->getKey(), $id);
        $parentStepEl = (int)($request['sectionId'] ?? 0)
            ? $stepEl?->load('parent')?->getRelation('parent')
            : null;
        $premisesIds = [];

        $stepEl->angles->each(static function (StepElementAngle $angle) use (&$premisesIds) {
            $angle->masks->each(static function (StepElementAngleMask $mask) use (&$premisesIds) {
                $premisesIds[$mask->estate_premises_id] = $mask->estate_premises_id;
            });
        });

        $stepOfFloor = $this->stepElementRepository->getOtherFloorsByFloor(
            $this->selector->getKey(),
            $id,
            $stepEl->parent_id,
            $this->config->types
        );
        $floorPremisesCount = $stepOfFloor->isNotEmpty()
            ? $this->getStat(
                $request,
                $stepOfFloor->pluck('estate_model_id')->toArray(),
                $parentStepEl?->estate_model_id ?? 0
            )
            : [];

        $stepOfFloor = $stepOfFloor->each(static function (StepElement $el) use ($id, $floorPremisesCount) {
            $el->active = $el->id === $id;
            $el->disabled = !$floorPremisesCount->has($el->estate_model_id);
            $el->premisesCount = $floorPremisesCount->get($el->estate_model_id, 0);
        });

        $step = Factory::make($stepEl->step, $this->selector);

        if (!str_contains(mb_strtolower($stepEl->title), mb_strtolower($step->getTitle()))) {
            $stepEl->title = $step->getTitle() . ' ' . $stepEl->title;
        }
        unset($step);

        $res->put('floors', $stepOfFloor);
        $res->put('breadcrumbs', $this->buildBreadCrumbs($stepEl));
        $res->put('filters', $this->getForm($request));
        $stepEl->title = $stepEl->getOriginal('title');

        if (!$premisesIds) {
            $res->put('step', $stepEl);

            return $res;
        }

        $premises = $this->getPremises($request, $premisesIds)->pluck(null, 'id');
        $stepEl->angles->map(static function (StepElementAngle $angle) use ($premises) {
            $angle->masks = $angle->masks->each(function (StepElementAngleMask $mask, $key) use ($premises, $angle) {
                if (!isset($premises[$mask->estate_premises_id])) {
                    $angle->masks->forget($key);
                    return;
                }

                $mask->setRelation('premises', $premises[$mask->estate_premises_id]);
            });

            return $angle;
        });
        unset($premises);

        $res->put('step', $stepEl);
        $res->put('settings', $this->selector->settings);

        Cache::tags([
            $this->estateService->getModuleCacheTag(),
            $this->selectorService->getCacheTag($this->selector)
        ])->put($cacheId, $res, self::CACHE_TTL);

        return $res;
    }

    private function getStat(array $request, array $floorIds, int $sectionId = 0): Collection
    {
        $floorFilter = new FloorBase();
        $floorFilter->setRequestValues([$floorFilter->getName() => $floorIds]);
        $this->addFilter($floorFilter);

        if ($sectionId) {
            $sectionFilter = new SectionBase();
            $sectionFilter->setRequestValues([$sectionFilter->getName() => [$sectionId]]);
            $this->addFilter($sectionFilter);
        }

        return $this->searchRepository->getFloorsWithPremisesCount($this->getDataFilter($request));
    }

    private function getPremises(array $request, array $premisesIds): Collection
    {
        $data = $this->searchRepository->getPremises(
            $this->getDataFilter($request, [AbstractFilter::PARAM_STATUSES]),
            $premisesIds
        );

        if ($data->isEmpty()) {
            return $data;
        }

        $typeId = $data->first()?->type?->group_id;
        $cardRoutes = new Collection();

        /** @var PageLinkService $pageLinkService */
        $pageLinkService = resolve(PageLinkService::class);

        if ($typeId) {
            $cardRoutes->put(
                $typeId,
                $pageLinkService->getRouteNameByElement(
                    resolve(SiteService::class)->current(),
                    PremisesTypeGroup::class,
                    $typeId
                )
            );
        }

        return $this->estateService->preparePremises($data, $cardRoutes);
    }
}
