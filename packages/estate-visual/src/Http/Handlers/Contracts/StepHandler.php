<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Handlers\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Kelnik\Core\Http\Controllers\BaseApiController;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateVisual\Http\Resources\MaskTypes\MaskTypeFactory;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;
use Kelnik\EstateVisual\Models\Filters\Base;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractFilter;
use Kelnik\EstateVisual\Models\Filters\Contracts\Filter;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Repositories\Contracts\SearchRepository;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;
use Kelnik\EstateVisual\Services\Contracts\SelectorService;

abstract class StepHandler extends BaseApiController
{
    protected const CACHE_TTL = 864000; // 10 days

    protected readonly EstateService $estateService;
    protected readonly SelectorService $selectorService;
    protected readonly SelectorRepository $selectorRepository;
    protected readonly StepElementRepository $stepElementRepository;
    protected readonly SearchRepository $searchRepository;
    protected Collection $filters;
    protected readonly ?Selector $selector;

    public function __construct(int|string $selectorKey, protected readonly SearchConfig $config)
    {
        $this->selectorRepository = resolve(SelectorRepository::class);
        $this->selector = $this->selectorRepository->findByPrimary($selectorKey);

        if (!$this->selector || !$this->selector->exists) {
            throw new InvalidArgumentException('Selector not found');
        }

        $this->estateService = resolve(EstateService::class);
        $this->selectorService = resolve(SelectorService::class);
        $this->stepElementRepository = resolve(StepElementRepository::class);
        $this->searchRepository = resolve(SearchRepository::class);
        $this->filters = new Collection();

        $this->initFilters();
    }

    abstract public function handle(array $request): Collection;

    protected function buildBreadCrumbs(StepElement $step): Collection
    {
        $breadCrumbs = new Collection();

        $this->stepElementRepository->getPrevSteps(
            $this->selector->getKey(),
            $step->getKey(),
            Factory::make($step->step, $this->selector)->getAllowedPrev()
        )->each(function (StepElement $el) use ($breadCrumbs, &$link) {
            $mask = new StepElementAngleMask(['type' => $el->step]);
            $mask->setRelation('element', $el);
            $link .= MaskTypeFactory::make(
                $mask,
                $this->selector->settings,
                $this->config,
                new Collection()
            )->getLink();

            $breadCrumbs->add(['link' => $link, 'name' => $el->title]);
        });

        $breadCrumbs->add([
            'link' => null,
            'name' => $step->title
        ]);

        return $breadCrumbs;
    }

    // Data filter
    public function initFilters(): void
    {
        $baseFilter = new Base();
        $baseFilter->setRequestValues([
            AbstractFilter::PARAM_TYPES => $this->config->types,
            AbstractFilter::PARAM_STATUSES => $this->config->statuses
        ]);

        $this->addFilter($baseFilter);
        unset($baseFilter);

        foreach ($this->config->filters as $filterName) {
            $this->addFilter(new $filterName($this->selector->settings));
        }
    }

    public function addFilter(Filter $filter): void
    {
        $this->filters->put($filter::class, $filter);
    }

    public function getForm(array $request): Collection
    {
        return new Collection([
            'baseBorders' => $this->getBorders(),
            'currentBorders' => $this->getBorders($request)
        ]);
    }

    protected function getBorders(array $request = []): Collection
    {
        $borders = new Collection();

        if ($this->filters->isEmpty()) {
            return $borders;
        }

        $dataFilter = $this->getDataFilter($request);
        $cacheId = $this->getCacheId('borders', $dataFilter);
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $this->filters->each(static function (Filter $filter) use (&$borders, $dataFilter) {
            if (!$filter->isHidden()) {
                $borders->put($filter->getName(), $filter->getResult($dataFilter));
            }
        });

        unset($dataFilter);

        Cache::tags([
            $this->estateService->getModuleCacheTag(),
            $this->selectorService->getCacheTag($this->selector)
        ])->put($cacheId, $borders, self::CACHE_TTL);

        return $borders;
    }

    protected function getDataFilter(array $request, array $exclude = []): Collection
    {
        $dataFilter = new Collection();

        $this->filters->each(function (Filter $filter) use (&$dataFilter, $request, $exclude) {
            if (!$filter->isHidden() && $request) {
                $filter->setRequestValues($request);
            }

            $filter->setAdditionalValues([
                AbstractFilter::PARAM_TYPES => $this->config->types,
                AbstractFilter::PARAM_STATUSES => $this->config->statuses
            ]);

            $filter->setExcludeParams($exclude);

            $params = $filter->getDataFilterParams();

            if ($params->isNotEmpty()) {
                $dataFilter = $dataFilter->merge($params);
            }
        });

        return $dataFilter;
    }

    // Cache
    protected function getCacheId(string $prefix, array|Collection $filterValues): string
    {
        return 'estateVisual_' . $prefix . '_' .
            md5(is_array($filterValues) ? json_encode($filterValues) : $filterValues->toJson());
    }
}
