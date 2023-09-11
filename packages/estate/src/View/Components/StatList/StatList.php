<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\StatList;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;
use Kelnik\Estate\Repositories\Contracts\StatRepository;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\Estate\View\Components\Contracts\ComponentDto;
use Kelnik\Page\Services\Contracts\PageService;

final class StatList extends Component implements KelnikComponentCache, KelnikComponentAlias
{
    protected const CACHE_TTL_DEFAULT = 864000; // 10 days

    private int $pageId = 0;
    private int $pageComponentId = 0;
    private array $types = [];
    private ?string $template = null;

    private DateTimeInterface|DateInterval|int $cacheTtl = self::CACHE_TTL_DEFAULT;
    private readonly StatRepository $statRepository;
    private readonly EstateService $estateService;
    private readonly PageService $pageService;

    public function __construct(?ComponentDto $params = null)
    {
        foreach (['pageId', 'pageComponentId', 'types', 'template'] as $propertyName) {
            if (isset($params->{$propertyName})) {
                $this->{$propertyName} = $params->{$propertyName};
            }
        }
        unset($params);

        $this->statRepository = resolve(StatRepository::class);
        $this->estateService = resolve(EstateService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getAlias(): string
    {
        return 'kelnik-estate-stat-list';
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $cacheTags = [
            $this->estateService->getModuleCacheTag()
        ];

        if ($this->pageId) {
            $cacheTags[] = $this->pageService->getPageCacheTag($this->pageId);
        }

        if ($this->pageComponentId) {
            $cacheTags[] = $this->pageService->getPageComponentCacheTag($this->pageComponentId);
        }

        $res = [];
        $stat = $this->statRepository->getStatByTypes(array_column($this->types, 'id'));

        foreach ($this->types as $el) {
            if (!isset($stat[$el['id']])) {
                continue;
            }
            $el['priceMin'] = Arr::get($stat, $el['id'] . '.price_min', 0);
            $el['areaMin'] = Arr::get($stat, $el['id'] . '.area_min', 0);
            $res['list'][$el['id']] = $el;
        }
        unset($stat);

        Cache::tags($cacheTags)->put($cacheId, $res, $this->cacheTtl);

        return $res;
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        return !empty($data['list'])
                ? \Illuminate\Support\Facades\View::first(
                    [$this->template ?? 'kelnik-estate::components.statList.template'],
                    $data
                )
                : null;
    }

    public function getCacheId(): string
    {
        return $this->estateService->getModuleCacheTag() . '_stat_' . $this->pageComponentId;
    }
}
