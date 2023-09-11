<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\OtherList;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;
use Kelnik\News\Models\Element;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\News\View\Components\Contracts\ComponentDto;
use Kelnik\Page\Services\Contracts\PageService;

final class OtherList extends Component implements
    KelnikComponentCache,
    KelnikComponentAlias
{
    protected const CACHE_TTL_DEFAULT = 864000; // 10 days

    private int $categoryId = 0;
    /** @var int Excluded row primary value */
    private int $primary = 0;
    private int $pageId = 0;
    private int $count = 0;
    private ?string $title = null;
    private ?Collection $cardRoutes = null;
    private ?string $template = null;

    private DateTimeInterface|DateInterval|int $cacheTtl = self::CACHE_TTL_DEFAULT;
    private readonly NewsService $newsService;
    private readonly PageService $pageService;

    public function __construct(?ComponentDto $params = null)
    {
        foreach (['pageId', 'categoryId', 'primary', 'count', 'title', 'cardRoutes', 'template'] as $propertyName) {
            if (isset($params->{$propertyName})) {
                $this->{$propertyName} = $params->{$propertyName};
            }
        }
        unset($params);
        $this->newsService = resolve(NewsService::class);
        $this->pageService = resolve(PageService::class);
    }

//    public static function getModuleName(): string
//    {
//        return NewsServiceProvider::MODULE_NAME;
//    }
//
//    public static function getTitle(): string
//    {
//        return trans('kelnik-news::admin.components.otherList.title');
//    }

    public static function getAlias(): string
    {
        return 'kelnik-news-other-list';
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $res['list'] = $this->newsService->getListByElement(
            $this->categoryId,
            $this->primary,
            $this->count,
            $this->cardRoutes
        );

        if ($res['list']->isNotEmpty()) {
            $res['list']->each(function (Element $el) {
                if ($el->active_date_finish) {
                    $this->cacheTtl = $this->newsService->getMinCacheTime(
                        $el->active_date_finish->diffInSeconds(now(), false),
                        $this->cacheTtl
                    );
                }
            });
        }

        $cacheTags = [$this->newsService->getCategoryCacheTag($this->categoryId)];

        $this->cardRoutes->each(
            function (string $routeName) use (&$cacheTags) {
                $cacheTags[] = $this->pageService->getDynComponentCacheTag($routeName);
            }
        );

        Cache::tags($cacheTags)->put($cacheId, $res, $this->cacheTtl);

        return $res;
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        return !empty($data['list']) && $data['list']->isNotEmpty()
                ? view($this->template ?? 'kelnik-news::components.otherList.news', $data)->with('title', $this->title)
                : null;
    }

    public function getCacheId(): string
    {
        return $this->newsService->getElementCacheTag(
            'page_' . $this->pageId .
            '_other_' . $this->primary
        );
    }
}
