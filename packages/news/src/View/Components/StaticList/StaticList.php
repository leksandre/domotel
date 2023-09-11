<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\StaticList;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Providers\NewsServiceProvider;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class StaticList extends KelnikPageComponent implements HasContentAlias
{
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
        return trans('kelnik-news::admin.components.staticList.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-news-staticList';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = $this->getComponentData()->getValue()?->get('content');
        $content['template'] = $this->getComponentData()->getValue()?->get('template') ?? '';
        $content['template'] = self::getTemplates()->first(
            static fn(StaticListTemplate $el) => $el->name === $content['template']
        ) ?? self::getTemplates()->first();

        /** @var StaticListTemplate $template */
        $template = $content['template'];
        $content['template'] = $content['template']->name;
        $categories = $content['categories'] ?? null;
        $cardRoutes = $this->pageLinkService->getElementRoutes(
            resolve(SiteService::class)->current(),
            [Category::class => $categories]
        )->map(fn(PageComponentRoute $route) => $this->pageLinkService->getPageComponentRouteName($route));

        $callBack = null;

        if ($template->imageBreakPoints) {
            $callBack = static function (Element $el) use ($template) {
                if (!$el->preview_image || !$el->previewImage->exists) {
                    return;
                }

                $el->previewImagePicture = Picture::init(new ImageFile($el->previewImage))
                        ->setLazyLoad(true)
                        ->setBreakpoints($template->imageBreakPoints)
                        ->setImageAttribute('alt', $previewImage->alt ?? '')
                        ->render();
            };
        }

        /** @var Collection $list */
        $list = resolve(ElementRepository::class)->getList(
            $categories,
            $content['limit'] ?? config('kelnik-news.pagination.limit')
        );

        $list = $this->newsService->prepareElements($list, $cardRoutes, $callBack);
        unset($template, $callBack);

        $list->each(function (Element $el) {
            if ($el->active_date_finish) {
                $this->cacheTtl = $this->newsService->getMinCacheTime(
                    $el->active_date_finish->diffInSeconds(now(), false),
                    $this->cacheTtl
                );
            }
        });
        $cacheTags = [$this->pageService->getPageComponentCacheTag($this->pageComponent->id)];

        $cardRoutes->each(function (string $routeName) use (&$cacheTags) {
            $cacheTags[] = $this->pageService->getDynComponentCacheTag($routeName);
        });

        foreach ($categories as $el) {
            $cacheTags[] = $this->newsService->getCategoryCacheTag($el);
        }

        $content['list'] = $list;
        unset($list, $categories, $el);

        Cache::tags($cacheTags)->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();
        $template = $data['template'];
        unset($data['template']);

        return isset($data['list']) && $data['list']->isNotEmpty()
                ? view($template, $data)
                : null;
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new StaticListTemplate(
                'kelnik-news::components.staticList.slider',
                trans('kelnik-news::admin.components.staticList.templates.slider'),
                [
                    1440 => 400,
                    1280 => 360,
                    960 => 516,
                    670 => 387,
                    320 => 558
                ]
            ),
            new StaticListTemplate(
                'kelnik-news::components.staticList.mosaic',
                trans('kelnik-news::admin.components.staticList.templates.mosaic'),
                [
                    1440 => 400,
                    1280 => 360,
                    960 => 516,
                    670 => 387,
                    320 => 558
                ]
            )
        ]);
    }
}
