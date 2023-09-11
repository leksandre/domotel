<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\ElementCard;

use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Providers\NewsServiceProvider;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\News\View\Components\OtherList\OtherListDto;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\RouteProvider;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageDynamicComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Symfony\Component\HttpFoundation\Response;

final class ElementCard extends KelnikPageComponent implements KelnikPageDynamicComponent
{
    private ?string $slug;
    private ?Element $element;
    private readonly NewsService $newsService;
    private readonly PageLinkService $pageLinkService;
    private readonly PageService $pageService;

    public function __construct()
    {
        $this->newsService = resolve(NewsService::class);
        $this->pageLinkService = resolve(PageLinkService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return NewsServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-news::admin.components.elementCard.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-news-element-card';
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public static function initRouteProvider(Page $page, PageComponent $pageComponent): RouteProvider
    {
        return new \Kelnik\News\View\Components\ElementCard\RouteProvider($page, $pageComponent);
    }

    protected function getTemplateData(): array
    {
        $currentRoute = Route::current();
        $this->slug = $currentRoute->parameter('slug');

        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $res = [];
        $this->element = resolve(ElementRepository::class)->findActiveBySlug($this->slug);

        if (!$this->element->exists) {
            $this->saveCache($cacheId, $res, $currentRoute);

            return $res;
        }

        $routeName = $this->pageLinkService->getRouteNameByElement(
            resolve(SiteService::class)->current(),
            Category::class,
            $this->element->category->getKey()
        );

        if ($routeName !== $currentRoute->getName()) {
            $this->saveCache($cacheId, $res, $currentRoute);

            return $res;
        }

        $template = $this->getComponentData()->getValue()?->get('template') ?? '';
        /** @var ElementCardTemplate $template */
        $template = self::getTemplates()->first(
            static fn(ElementCardTemplate $el) => $el->name === $template
        ) ?? self::getTemplates()->first();

        $other = $this->getComponentData()->getValue()?->get('other') ?? [];
        $other['count'] ??= config('kelnik-news.card.otherElementsCount');

        $listParams = new OtherListDto();
        $listParams->categoryId = $this->element->category->getKey();
        $listParams->primary = $this->element->getKey();
        $listParams->title = $other['title'] ?? '';
        $listParams->count = (int) $other['count'] ?? 0;
        $listParams->pageId = $this->page->id;
        $listParams->pageComponentId = $this->pageComponent->id;
        $listParams->template = $template->otherListTemplate;
        $listParams->cardRoutes = new Collection([$this->element->category->getKey() => $currentRoute->getName()]);

        $tmp = $this->newsService->prepareElements(
            new Collection([$this->element]),
            $listParams->cardRoutes
        );
        $this->element = $tmp->first();
        unset($tmp);

        $res = [
            'element' => $this->element,
            'listParams' => $listParams,
            'template' => $template->name
        ];
        unset($listParams, $template);

        $res['breadcrumbs'] = $this->pageService->getBreadcrumbs($this->page);
        $res['breadcrumbs'][] = [Str::limit($this->element->title), url()->current()];

        $this->saveCache($cacheId, $res, $currentRoute);

        return $res;
    }

    private function saveCache(string $cacheId, array $res, \Illuminate\Routing\Route $route): void
    {
        $tags = [
            $this->pageService->getPageComponentCacheTag($this->pageComponent->getKey()),
            $this->pageService->getDynComponentCacheTag($route->getName())
        ];

        if ($this->element?->exists) {
            if ($this->element->category?->exists) {
                $tags[] = $this->newsService->getCategoryCacheTag($this->element->category->getKey());
            }
            $tags[] = $this->newsService->getElementCacheTag($this->element->getKey());
        }

        Cache::tags($tags)->put(
            $cacheId,
            $res,
            $this->newsService->getMinCacheTime(
                $this->cacheTtl,
                $this->element?->active_date_finish?->diffInSeconds(now(), false) ?? 0
            )
        );
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        abort_if(empty($data['element']), Response::HTTP_NOT_FOUND);

        $this->setMeta($data['element']);

        $template = $data['template'] ?? 'kelnik-news::components.element-card';
        unset($data['template']);

        return view($template, $data);
    }

    private function setMeta(Element $element): void
    {
        SEOMeta::setTitle($element->title);
        OpenGraph::setTitle($element->title);

        foreach (['title', 'description', 'keywords'] as $tag) {
            $getMethod = 'get' . ucfirst($tag);
            $setMethod = 'set' . ucfirst($tag);
            $val = $element->meta->{$getMethod}();

            if (!$val) {
                continue;
            }

            SEOMeta::{$setMethod}($val);

            if ($tag === 'keywords') {
                continue;
            }

            OpenGraph::{$setMethod}($val);
        }

        $image = $element->relationLoaded('bodyImage') && $element->bodyImage->exists
            ? $element->bodyImage
            : null;

        if ($image) {
            OpenGraph::addImage($image->url());
        }
    }

    public static function getTemplates(): Collection
    {
        $res = new Collection([
            new ElementCardTemplate(
                'kelnik-news::components.elementCard.news',
                trans('kelnik-news::admin.components.elementCard.templates.news')
            )
        ]);

        $tmp = new ElementCardTemplate(
            'kelnik-news::components.elementCard.action',
            trans('kelnik-news::admin.components.elementCard.templates.action')
        );

        $tmp->otherListTemplate = 'kelnik-news::components.otherList.actions';

        $res->add($tmp);
        unset($tmp);

        return $res;
    }

    public function getCacheId(): string
    {
        return $this->newsService->getElementCacheTag(
            'page_' . $this->page->id .
            '_card_' . md5($this->slug ?? '')
        );
    }
}
