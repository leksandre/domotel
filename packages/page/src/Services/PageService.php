<?php

declare(strict_types=1);

namespace Kelnik\Page\Services;

use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Core\Theme\Contracts\Color;
use Kelnik\Page\Http\Controllers\PageController;
use Kelnik\Page\Http\Middleware\Contracts\PageComponentMiddleware;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteRepository;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponentSection;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

final class PageService implements Contracts\PageService
{
    public function __construct(
        private readonly PageRepository $repository,
        private readonly SiteService $siteService,
        private readonly \Kelnik\Page\Services\Contracts\PageLinkService $pageLinkService
    ) {
    }

    public function getActivePageByPrimary(int|string $siteId, int|string $pageId): Page
    {
        $cacheId = $this->getPageCacheKey($pageId, $siteId);
        $page = Cache::get($cacheId);

        if ($page !== null) {
            return $page;
        }

        $page = $this->repository->getActivePageByPrimary($pageId, $siteId);

        if ($page->exists) {
            Cache::tags($this->getPageCacheTag($pageId))->forever($cacheId, $page);
        }

        return $page;
    }

    public function getActivePageWithComponent(int|string $siteId, int|string $pageId, int|string $componentId): array
    {
        $page = $this->getActivePageByPrimary($siteId, $pageId);

        if (!$page->exists) {
            return [$page, new PageComponent()];
        }

        $components = $this->getPageActiveComponents($page);

        return [
            $page,
            $components->first(
                static fn(PageComponent $component) => $component->getKey() === $componentId
            ) ?? new PageComponent()
        ];
    }

    public function getPageByUrl(int|string $siteId, string $path)
    {
        $cacheId = $this->getPageCacheKey(md5(trim($path, '/')), $siteId);
        $page = Cache::get($cacheId);

        if ($page !== null) {
            return $page;
        }

        $page = $this->repository->getPageByPath($path, $siteId);

        if ($page->exists) {
            Cache::tags($this->getPageCacheTag($page->getKey()))->forever($cacheId, $page);
        }

        return $page;
    }

    public function getPageContent(Page $page, Request $request, array $stacks = []): array
    {
        $res = [
            KelnikPageComponentSection::PAGE_COMPONENT_SECTION_HEADER => '',
            KelnikPageComponentSection::PAGE_COMPONENT_SECTION_CONTENT => '',
            KelnikPageComponentSection::PAGE_COMPONENT_SECTION_FOOTER => '',
            'stacks' => [],
            'cssClasses' => $page->css_classes
        ];

        if ($stacks) {
            foreach ($stacks as $stackName) {
                $res['stacks'][$stackName] = '';
            }
        }

        $pageComponents = $this->getPageActiveComponents($page);

        if ($pageComponents->isEmpty()) {
            return $res;
        }

        $pageComponents->each(function (PageComponent $pageComponent) use ($page, $request, &$res, $stacks) {
            if (!class_exists($pageComponent->component)) {
                return;
            }

            $section = $pageComponent->component::getPageComponentSection()
                ?? KelnikPageComponentSection::PAGE_COMPONENT_SECTION_CONTENT;
            $this->initViewComponent($page, $pageComponent, $request)
                ->render()
                ?->render(function (View $view, ?string $html) use ($section, &$res, $stacks) {
                    $res[$section] .= $html;
                    if (!$stacks) {
                        return;
                    }
                    foreach ($stacks as $stackName) {
                        $res['stacks'][$stackName] .= trim($view->getFactory()->yieldPushContent($stackName));
                    }
                });
        });

        return $res;
    }

    public function getPagesWithoutDynamicComponents(): Collection
    {
        $pages = $this->repository->getPagesWithoutDynamicComponents();

        if ($pages->isEmpty()) {
            return $pages;
        }

        $sites = $this->siteService->getAll()->keyBy('id');

        return $pages->map(function (Page $page) use ($sites) {
            $page->site = $sites[$page->site_id] ?? null;
            $page->title = '[' . ($page->site?->title ?? '-') . '] ' . $page->title;

            return $page;
        });
    }

    public function getSitePagesWithoutDynamicComponents(int|string $siteId): Collection
    {
        return $this->repository->getPagesWithoutDynamicComponents($siteId);
    }

    public function initViewComponent(
        Page &$page,
        PageComponent &$pageComponent,
        ?Request $request = null
    ): KelnikPageComponent {
        $viewComponent = resolve($pageComponent->component);
        $viewComponent->setPage($page);
        $viewComponent->setPageComponent($pageComponent);

        if ($request) {
            $viewComponent->setRequest($request);
        }

        return $viewComponent;
    }

    public function loadPageRoutes(): void
    {
        $pages = $this->repository->getActivePageRoutes(
            app()->runningUnitTests() || app()->runningInConsole(),
            self::PAGE_ROUTES_CACHE
        );

        if ($pages->isEmpty()) {
            return;
        }

        $sites = $this->siteService->getActive()->keyBy('id');
        $siteHosts = [];

        foreach ($sites as $site) {
            if ($site->hosts->isEmpty()) {
                continue;
            }

            $hosts = array_filter($site->hosts->pluck('value')->toArray());
            $hosts = array_map([$this, 'normalizeHost'], $hosts);
            $siteHosts[$site->getKey()] = $hosts;
        }

        $pages = $pages->sort(static function (Page $curPage, Page $nextPage) use ($sites) {
            $curPageUrl = $curPage->getUrl();
            $nextPageUrl = $nextPage->getUrl();

            /**
             * @var ?Site $curPageSite
             * @var ?Site $nextPageSite
             */
            $curPageSite = $sites[$curPage->site_id] ?? null;
            $nextPageSite = $sites[$nextPage->site_id] ?? null;

            if (!$curPageSite?->primary && $nextPageSite?->primary) {
                return -1;
            } elseif ($curPageSite?->primary && !$nextPageSite?->primary) {
                return 1;
            }

            return mb_strlen($curPageUrl) <=> mb_strlen($nextPageUrl)
                ? $nextPageUrl <=> $curPageUrl
                : $nextPage->priority <=> $curPage->priority;
        });

        foreach ($pages as $page) {
            $page->site = $sites[$page->site_id] ?? null;

            if (!$page->site) {
                continue;
            }

            if ($page->activeComponents->isEmpty()) {
                if ($page->site->primary) {
                    $this->addPageRoute($page);
                    continue;
                }

                if (empty($siteHosts[$page->site_id])) {
                    $this->addPageRoute($page);
                    continue;
                }

                foreach ($siteHosts[$page->site_id] as $host) {
                    $this->addPageRoute($page, $host);
                }

                continue;
            }

            $page->activeComponents->each(function (PageComponent $pageComponent) use ($page, $siteHosts) {
                Route::middleware(['web'])->group(function () use ($page, $pageComponent, $siteHosts) {
                    $pageComponent->routes
                        ->sortByDesc(static fn(PageComponentRoute $curRoute) => mb_strlen($curRoute->path))
                        ->each(
                            function (PageComponentRoute $pageComponentRoute) use ($page, $pageComponent, $siteHosts) {
                                $defaultValues = [
                                    $this->siteService::ROUTE_PARAM_NAME => $page->site_id
                                ];

                                if ($pageComponentRoute->ignore_page_slug) {
                                    $defaultValues[self::ROUTE_PARAM_NAME] = $pageComponent->page_id;
                                }

                                $pageComponentRoute->middlewares ??= [];
                                $pageComponentRoute->middlewares = array_map(
                                    function (string $middleware) use ($pageComponent) {
                                        if (!is_a($middleware, PageComponentMiddleware::class, true)) {
                                            return $middleware;
                                        }

                                        $middleware .= ':' . $pageComponent->page_id . ',' . $pageComponent->getKey();

                                        return $middleware;
                                    },
                                    $pageComponentRoute->middlewares
                                );

                                if ($page->site->primary) {
                                    $this->addPageComponentRoute($page, $pageComponentRoute, $defaultValues);
                                    return;
                                }

                                foreach ($siteHosts[$page->site_id] as $host) {
                                    $this->addPageComponentRoute($page, $pageComponentRoute, $defaultValues, $host);
                                }
                            }
                        );
                });
            });
        }
    }

    private function addPageRoute(Page $page, ?string $host = null): void
    {
        Route::domain($host)
            ->middleware(['web'])
            ->match(
                ['get', 'post'],
                $page->getUrl(),
                [PageController::class, 'show']
            )
            ->name($this->getPageRouteName($page))
            ->setDefaults([
                $this->siteService::ROUTE_PARAM_NAME => $page->site_id,
                self::ROUTE_PARAM_NAME => $page->getKey()
            ]);
    }

    private function addPageComponentRoute(
        Page $page,
        PageComponentRoute $pageComponentRoute,
        array $defaultValues,
        ?string $host = null
    ): void {
        Route::domain($host)
            ->prefix($pageComponentRoute->ignore_page_slug ? $page->getParentUrl() : $page->getUrl())
            ->middleware($pageComponentRoute->middlewares)
            ->match(
                ['get', 'post'],
                $pageComponentRoute->path,
                [PageController::class, 'show']
            )
            ->name($this->pageLinkService->getPageComponentRouteName($pageComponentRoute))
            ->where($pageComponentRoute->params->toArray())
            ->setDefaults($defaultValues);
    }

    private function normalizeHost(?string $host): ?string
    {
        return $host ? idn_to_ascii($host) : null;
    }

    public function getDynamicPageRouteNameById(int|string $pageId): ?string
    {
        if (!$pageId) {
            return null;
        }

        $pageComponentRoute = resolve(PageComponentRepository::class)?->getPageDynamicComponentFirstRoute($pageId);

        return $pageComponentRoute ? $this->pageLinkService->getPageComponentRouteName($pageComponentRoute) : null;
    }

    public function getPageComponentRoutesByPrimaryKey(int|string $pageComponentRouteId): PageComponentRoute
    {
        return resolve(PageComponentRouteRepository::class)->findByPrimary($pageComponentRouteId);
    }

    public function getPageUrl(Page $page, array $params = [], bool $absolute = true): string
    {
        try {
            $page->site = $this->siteService->findByPrimaryKey($page->site_id);

            return route($this->getPageRouteName($page), $params, $absolute);
        } catch (UrlGenerationException | RouteNotFoundException $e) {
            return $page->getUrl();
        }
    }

    public function getPageComponentUrl(
        Page $page,
        PageComponent $pageComponent,
        array $params = [],
        bool $absolute = true
    ): string {
        $url = $this->getPageUrl($page, $params, $absolute);

        $pageComponentAlias = $this->getPageComponentContentAlias($pageComponent, $page);

        if ($pageComponentAlias) {
            $url .= $pageComponentAlias;
        }

        return $url;
    }

    public function getPageComponentContentAlias(PageComponent $pageComponent, ?Page $page = null): ?string
    {
        if (!is_a($pageComponent->component, HasContentAlias::class, true)) {
            return null;
        }

        if ($page === null) {
            $page = $pageComponent->page;
        }

        $viewComponent = $this->initViewComponent($page, $pageComponent);
        $alias = $viewComponent->getContentAlias();
        unset($viewComponent);

        return $alias ? '#' . $alias : null;
    }

    public function prepareComponentColorsFromRequest(Collection $colors, array $requestColorValues): Collection
    {
        $colorKeys = [];
        $colors = $colors->map(static function (Color $color) use (&$colorKeys, $requestColorValues) {
            if (isset($requestColorValues[$color->getFullName()])) {
                $color->setValue($requestColorValues[$color->getFullName()]);
            }

            $colorKeys[] = $color->getName();

            return $color;
        });

        /** @var SettingsService $settingsService */
        $settingsService = resolve(SettingsService::class);
        $defColors = $settingsService->getCurrentColors($colorKeys);
        $defColors = collect($defColors);

        return $settingsService->prepareColors($colors, $defColors);
    }

    public function getBreadcrumbs(Page $page, int $level = 0): array
    {
        $res = [];

        if (!$level) {
            $res[] = [trans('kelnik-page::front.homePage'), '/'];
        }

        if ($page->hasParent()) {
            $parent = $page->parent;
            $res[] = [$parent->title, $parent->getUrl()];

            if ($parent->hasParent()) {
                $res = array_merge($res, $this->getBreadcrumbs($parent, $level + 1));
            }
        }

        return $res;
    }

    public function getPageUrlById(string|int $id): string
    {
        if (!$id) {
            return '';
        }

        $page = $this->repository->findByPrimary($id);

        return $page->exists && $page->active
            ? route($this->getPageRouteName($page), [], false)
            : '';
    }

    public function getPageRouteName(Page $page): string
    {
        return 'kelnik.page.' . $page->getKey();
    }

    private function getPageActiveComponents(Page $page): Collection
    {
        return Cache::rememberForever(
            $this->getPageActiveComponentsCacheKey($page->getKey()),
            static fn() => $page->activeComponents
        );
    }

    // Cache tags
    //
    public function getPageCacheTag(int|string $key): ?string
    {
        return self::getCacheTag((string)$key, self::PAGE_CACHE);
    }

    public function getPageCacheKey(int|string $key, int|string $siteId): ?string
    {
        return self::getPageCacheTag($key) . '_' . $siteId;
    }

    public function getPageActiveComponentsCacheKey(int $pageId): string
    {
        return self::PAGE_ACTIVE_COMPONENTS_CACHE . '_' . $pageId;
    }

    public function getPageComponentCacheTag(int|string $key): ?string
    {
        return self::getCacheTag((string)$key, 'pageComponent');
    }

    public function getDynComponentCacheTag(string $routeName): ?string
    {
        return self::getCacheTag($routeName, 'pageDynComponent');
    }

    private static function getCacheTag(string $key, string $prefix): ?string
    {
        $key = trim($key);

        return $key ? $prefix . '_' . $key : null;
    }
}
