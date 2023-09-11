<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\Element;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;
use Kelnik\News\Models\Category;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\News\View\Components\Contracts\ComponentDto;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;

final class Element extends Component implements
    KelnikComponentCache,
    KelnikComponentAlias
{
    protected const CACHE_TTL_DEFAULT = 864000; // 10 days

    protected DateTimeInterface|DateInterval|int $cacheTtl = self::CACHE_TTL_DEFAULT;

    private int|string $primary = 0;
    private array $templateData = [];
    private int $pageComponentId = 0;
    private ?string $template = null;

    private NewsService $newsService;
    private PageLinkService $pageLinkService;
    private PageService $pageService;
    public ?\Kelnik\News\Models\Element $action = null;

    public function __construct(?ComponentDto $params = null)
    {
        $props = ['primary', 'templateData', 'pageComponentId', 'template'];

        foreach ($props as $propertyName) {
            if (isset($params->{$propertyName})) {
                $this->{$propertyName} = $params->{$propertyName};
            }
        }

        $this->primary = (int)$this->primary;
        $this->newsService = resolve(NewsService::class);
        $this->pageLinkService = resolve(PageLinkService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getAlias(): string
    {
        return 'kelnik-news-element';
    }

    public function render(): View|Closure|string|null
    {
        $this->loadTemplateData();

        if (!$this->action?->exists) {
            return null;
        }

        $this->templateData['action'] = $this->action;

        return \Illuminate\Support\Facades\View::first(
            [$this->template, 'kelnik-news::components.element'],
            $this->templateData
        );
    }

    private function loadTemplateData(): void
    {
        if (!$this->primary) {
            return;
        }

        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res instanceof \Kelnik\News\Models\Element) {
            $this->action = $res;

            return;
        }

        $this->action = $this->newsService->getActiveRowByPrimary(
            $this->primary,
            [
                'id',
                'slug',
                'title',
                'preview',
                'show_timer',
                'active_date_start',
                'active_date_finish',
                'publish_date',
                'publish_date_start',
                'publish_date_finish',
                'button'
            ]
        );

        $cardRoutes = new Collection();

        if ($this->action->exists) {
            $routeName = $this->pageLinkService->getRouteNameByElement(
                resolve(SiteService::class)->current(),
                Category::class,
                $this->action->category->getKey()
            );

            if ($routeName) {
                $cardRoutes->put($this->action->category->getKey(), $routeName);
            }

            $tmp = $this->newsService->prepareElements(new Collection([$this->action]), $cardRoutes);
            $this->action = $tmp->first();
            unset($tmp);
        }

        $cacheTags = [
            $this->newsService->getCategoryCacheTag($this->action?->category_id ?? 0),
            $this->newsService->getElementCacheTag($this->action?->id ?? 0),
        ];

        $cardRoutes->each(
            function (string $routeName) use (&$cacheTags) {
                $cacheTags[] = $this->pageService->getDynComponentCacheTag($routeName);
            }
        );

        if ($this->pageComponentId) {
            $cacheTags[] = $this->pageService->getPageComponentCacheTag($this->pageComponentId);
        }

        Cache::tags(array_filter($cacheTags))->put($cacheId, $this->action, $this->getCacheTtl());
    }

    public function getCacheId(): string
    {
        return $this->newsService->getElementCacheTag($this?->action->id ?? $this->primary);
    }

    private function getCacheTtl(): int
    {
        if (!$this->action->exists) {
            return $this->cacheTtl;
        }

        return $this->newsService->getMinCacheTime(
            $this->cacheTtl,
            $this->action?->active_date_finish?->diffInSeconds(now(), false) ?? 0
        );
    }
}
