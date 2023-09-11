<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Services\SiteService;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateSearch\Models\Contracts\SearchConfig;
use Kelnik\EstateSearch\Models\Filters\Base;
use Kelnik\EstateSearch\Models\Filters\Contracts\Filter;
use Kelnik\EstateSearch\Models\Orders\Contracts\Order;
use Kelnik\EstateSearch\Providers\EstateSearchServiceProvider;
use Kelnik\EstateSearch\Repositories\Contracts\SearchRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;

final class SearchService implements Contracts\SearchService
{
    private const CACHE_TAG = EstateSearchServiceProvider::MODULE_NAME;
    private const CACHE_TTL = 864_000; // 10 days
    private const PAGE = 'page';
    private const PAGE_MIN = 1;

    private EstateService $estateService;
    private PageLinkService $pageLinkService;
    private PageService $pageService;
    private SearchRepository $repository;
    private Collection $filters;
    private Collection $orders;

    public function __construct(private readonly SearchConfig $config)
    {
        $this->estateService = resolve(EstateService::class);
        $this->pageLinkService = resolve(PageLinkService::class);
        $this->pageService = resolve(PageService::class);
        $this->repository = resolve(SearchRepository::class);

        $this->filters = new Collection();
        $this->orders = new Collection();

        $this->init();
    }

    private function init(): void
    {
        $baseFilter = new Base();
        $baseFilter->setRequestValues([
            self::PARAM_TYPES => $this->config->types,
            self::PARAM_STATUSES => $this->config->statuses
        ]);
        $this->addFilter($baseFilter);

        foreach (['filters', 'orders'] as $type) {
            if (empty($this->config->{$type})) {
                continue;
            }

            $method = $type === 'filters' ? 'addFilter' : 'addOrder';

            $isFirst = true;
            foreach ($this->config->{$type} as $v) {
                $className = $v['class'] ?? null;

                if (
                    !$className
                    || !class_exists($className)
                    || !in_array($className, config('kelnik-estate-search.' . $type))
                ) {
                    continue;
                }

                $cls = new $className();

                if ($cls instanceof Order) {
                    $cls->setTitle($v['titleAsc'] ??  null, $v['titleDesc'] ?? null);
                    $cls->setIsDefault($isFirst);
                } elseif ($cls instanceof Filter) {
                    $cls->setTitle($v['title'] ?? null);
                }

                call_user_func([$this, $method], $cls);
                $isFirst = false;
            }
        }

        unset($baseFilter);
    }

    public function addFilter(Filter $filter): void
    {
        $this->filters->put($filter::class, $filter);
    }

    public function addOrder(Order $order): void
    {
        $this->orders->put($order::class, $order);
    }

    public function getForm(array $request): Collection
    {
        return new Collection([
            'baseBorders' => $this->getBorders(),
            'currentBorders' => $this->getBorders($request),
            'count' => $this->getCount($request)
        ]);
    }

    private function getBorders(array $request = []): Collection
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

        $this->filters->each(function (Filter $filter) use (&$borders, $dataFilter) {
            if (!$filter->isHidden()) {
                $borders->put($filter->getName(), $filter->getResult($dataFilter));
            }
        });

        unset($dataFilter);

        Cache::tags($this->getCacheTags())->put($cacheId, $borders, self::CACHE_TTL);

        return $borders;
    }

    public function getResults(array $request): Collection
    {
        $dataFilter = $this->getDataFilter($request);

        $countTotal = $this->getCount($request);
        $page = (int)Arr::get($request, self::PAGE, self::PAGE_MIN);
        $pages = (int)ceil($countTotal / $this->config->pagination->perPage);

        if ($page < self::PAGE_MIN) {
            $page = self::PAGE_MIN;
        } elseif ($page > $pages) {
            $page = $pages;
        }

        /** @var ?Order $order */
        $order = $this->orders->first(function (Order $el) use ($request) {
            $el->setRequestValues($request);
            return $el->getDataOrder()->count();
        });
        $orderFields = $order ? $order->getDataOrder() : new Collection();

        $cacheId = $this->getCacheId('results', [$dataFilter, $orderFields, $page]);
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $data = $this->repository->getResults(
            $dataFilter,
            $orderFields,
            $this->config->pagination->perPage,
            ($page - 1) * $this->config->pagination->perPage
        );

        $cacheTags = $this->getCacheTags();

        if ($data->isNotEmpty()) {
            $cardRoutes = $this->getCardRouteByType($data->first()?->type?->group_id);
            if ($cardRoutes->isNotEmpty()) {
                $cacheTags[] = $this->pageService->getDynComponentCacheTag($cardRoutes->first() ?? '');
            }
            $data = $this->estateService->preparePremises($data, $cardRoutes);
        }

        $countMore = $data->count() < $this->config->pagination->perPage
            ? $data->count()
            : $this->config->pagination->perPage;
        $countLeft = max($countTotal - ($page * $this->config->pagination->perPage), 0);

        $res = new Collection([
            'count' => [
                'total' => $countTotal,
                'left' => $countLeft
            ],
            'pagination' => [
                'type' => $this->config->pagination->viewType,
                'current' => $page,
                'total' => $pages,
                'items' => $this->config->pagination->perPage,
                'buttonText' => trans(
                    'kelnik-estate-search::front.results.more',
                    [
                        'countMore' => $countMore,
                        'countLeft' => $countLeft,
                        'variants' => trans_choice('kelnik-estate-search::front.results.variants', $countMore)
                    ]
                )
            ],
            'sortOrder' => $this->orders,
            'items' => $data
        ]);

        Cache::tags($cacheTags)->put($cacheId, $res, self::CACHE_TTL);

        return $res;
    }

    public function getAllResults(array $request): Collection
    {
        $dataFilter = $this->getDataFilter($request);
        $countTotal = $this->getCount($request);

        /** @var ?Order $order */
        $order = $this->orders->first(function (Order $el) use ($request) {
            $el->setRequestValues($request);
            return $el->getDataOrder()->count();
        });
        $orderFields = $order ? $order->getDataOrder() : new Collection();

        $cacheId = $this->getCacheId('results', [$dataFilter, $orderFields]);
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $data = $this->repository->getResults($dataFilter, $orderFields);
        $cacheTags = $this->getCacheTags();

        if ((is_array($data) && $data) || $data->isNotEmpty()) {
            $cardRoutes = $this->getCardRouteByType($data[0]?->type?->group_id);
            $cacheTag = $this->pageService->getDynComponentCacheTag($cardRoutes->first() ?? '');
            if ($cacheTag) {
                $cacheTags[] = $cacheTag;
            }
            $data = $this->estateService->preparePremises($data, $cardRoutes);
        }

        $res = new Collection([
            'count' => $countTotal,
            'sortOrder' => $this->orders,
            'items' => $data
        ]);

        Cache::tags($cacheTags)->put($cacheId, $res, self::CACHE_TTL);

        return $res;
    }

    private function getCardRouteByType(int|string|null $typeId): Collection
    {
        static $site;

        $cardRoutes = new Collection();

        if (!$typeId) {
            return $cardRoutes;
        }

        if (!$site) {
            $site = resolve(SiteService::class)->current();
        }

        $routeName = $this->pageLinkService->getRouteNameByElement($site, PremisesTypeGroup::class, $typeId);

        if ($routeName && strlen($routeName)) {
            $cardRoutes->put($typeId, $routeName);
        }

        return $cardRoutes;
    }

    public function getCount(array $request): int
    {
        $dataFilter = $this->getDataFilter($request);
        $cacheId = $this->getCacheId('count', $dataFilter);
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $res = $this->repository->getCount($dataFilter);

        Cache::tags($this->getCacheTags())->put($cacheId, $res, self::CACHE_TTL);

        return $res;
    }

    public function getConfig(): ?SearchConfig
    {
        return $this->config;
    }

    private function getDataFilter(array $request): Collection
    {
        $dataFilter = new Collection();

        $this->filters->each(function (Filter $filter) use (&$dataFilter, $request) {
            if (!$filter->isHidden() && $request) {
                $filter->setRequestValues($request);
            }

            $filter->setAdditionalValues([
                self::PARAM_TYPES => $this->config->types,
                self::PARAM_STATUSES => $this->config->statuses
            ]);

            $params = $filter->getDataFilterParams();

            if ($params->isNotEmpty()) {
                $dataFilter = $dataFilter->merge($params);
            }
        });

        return $dataFilter;
    }

    private function getCacheTags(): array
    {
        return array_merge(
            [$this->estateService->getModuleCacheTag(), self::CACHE_TAG],
            $this->config->cacheTags
        );
    }

    private function getCacheId(string $prefix, array|Collection $filterValues): string
    {
        return 'estateSearch_' .
            $prefix .
            '_' .
            md5(
                json_encode($this->config) .
                (is_array($filterValues) ? json_encode($filterValues) : $filterValues->toJson())
            );
    }
}
