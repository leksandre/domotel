<?php

declare(strict_types=1);

namespace Kelnik\Page\Services;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Contracts\DynComponentDto;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Models\PageComponentRouteElement;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteElementRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteRepository;
use Kelnik\Page\Repositories\Contracts\PageRepository;

final class PageLinkService implements Contracts\PageLinkService
{
    public function __construct(
        private readonly PageRepository $repository,
        private readonly PageComponentRepository $componentRepository,
        private readonly PageComponentRouteRepository $componentRouteRepository,
        private readonly PageComponentRouteElementRepository $componentRouteElementRepository,
        private readonly SiteService $siteService
    ) {
    }

    public function getOptionListOfPagesWithDynComponent(array $componentNamespace): Collection
    {
        $sites = $this->siteService->getAll();
        $res = [];

        /** @var Site $site */
        foreach ($sites as $site) {
            $res[$site->getKey()] = [
                'id' => $site->getKey(),
                'title' => $site->title,
                'pages' => [
                    self::PAGE_MODULE_ROW_NO_PAGE => trans('kelnik-page::admin.optionList.noPage'),
                    self::PAGE_MODULE_ROW_NEW_PAGE => trans('kelnik-page::admin.optionList.newPage')
                ]
            ];
        }

        $pages = $this->repository->getPagesWithDynamicComponent(
            $componentNamespace,
            $sites->pluck('id')->toArray()
        );

        /** @var Page $page */
        foreach ($pages as $page) {
            $res[$page->site_id]['pages'][$page->getKey()] = $page->title;
        }

        return new Collection(array_values($res));
    }

    public function getPagesWithDynComponentByRouteElement(string $classNamespace, int|string $elementId): Collection
    {
        $componentRoutes = $this->componentRouteElementRepository->getByModelAndElementId($classNamespace, $elementId);

        if ($componentRoutes->isEmpty()) {
            return $componentRoutes;
        }

        $pages = [];

        /** @var PageComponentRouteElement $cRouteEl */
        foreach ($componentRoutes as $cRouteEl) {
            if (
                !$cRouteEl->relationLoaded('pageComponentRoute')
                || !$cRouteEl->pageComponentRoute->relationLoaded('pageComponent')
                || !$cRouteEl->pageComponentRoute->pageComponent->relationLoaded('page')
            ) {
                continue;
            }

            $page = &$cRouteEl->pageComponentRoute->pageComponent->page;

            $pages[$page->site_id][$cRouteEl->element_id] = $page->getKey();
        }

        return new Collection($pages);
    }

    public function getRouteNameByElement(
        Site $site,
        string $classNamespace,
        int|string $elementId
    ): ?string {
        $elements = $this->componentRouteElementRepository->getByModelAndElementId($classNamespace, $elementId);

        if ($elements->isEmpty()) {
            return null;
        }

        /** @var PageComponentRouteElement $el */
        foreach ($elements as $el) {
            if (
                !$el->relationLoaded('pageComponentRoute')
                || !$el->pageComponentRoute->relationLoaded('pageComponent')
                || !$el->pageComponentRoute->pageComponent->relationLoaded('page')
                || $el->pageComponentRoute->pageComponent->page->site_id !== $site->getKey()
            ) {
                continue;
            }

            return $this->getPageComponentRouteName($el->pageComponentRoute);
        }

        return null;
    }

    public function getElementRoutes(Site $site, array $modelElements): Collection
    {
        $res = new Collection();

        /** @var PageComponentRoute $el */
        foreach ($this->componentRouteElementRepository->getByModelElements($modelElements) as $el) {
            if (
                !$el->relationLoaded('pageComponentRoute')
                || !$el->pageComponentRoute->relationLoaded('pageComponent')
                || !$el->pageComponentRoute->pageComponent->relationLoaded('page')
                || $el->pageComponentRoute->pageComponent->page->site_id !== $site->getKey()
            ) {
                continue;
            }

            $res->put($el->element_id, $el->pageComponentRoute);
        }

        return $res;
    }

    public function deletePageComponentRouteElement(int|string $siteId, string $className, int|string $elementId): bool
    {
        return (bool)$this->componentRouteElementRepository
            ->deleteBySiteClassNameAndElement($siteId, $className, $elementId);
    }

    public function deletePageComponentRouteElements(string $className, int|string $elementId): bool
    {
        return (bool)$this->componentRouteElementRepository->deleteByClassNameAndElement($className, $elementId);
    }

    public function createOrUpdateLinkDynComponentToPage(
        array $sitePageId,
        DynComponentDto $dynComponentDto,
        string $elementClassName = null,
        int|string $elementId = null
    ): bool {
        foreach ($sitePageId as $siteId => $pageId) {
            $page = null;

            // Create or update page
            //
            $pageId = is_numeric($pageId) ? (int)$pageId : trim($pageId);

            // Do not create link to page
            if ($pageId === self::PAGE_MODULE_ROW_NO_PAGE) {
                // Delete all relations
                $page = new Page(['site_id' => $siteId]);
                $this->syncPageComponentRouteElement(
                    $page,
                    new PageComponentRoute(),
                    $elementClassName,
                    $elementId
                );
                continue;
            }

            // Create new page
            $isNewPage = false;

            if ($pageId === self::PAGE_MODULE_ROW_NEW_PAGE) {
                $page = new Page([
                    'site_id' => $siteId,
                    'active' => true,
                    'title' => $dynComponentDto->pageTitle,
                    'slug' => $dynComponentDto->pageSlug
                ]);

                $page = $this->makeUniquePageSlugAndTitle($page);

                if (!$page) {
                    continue;
                }

                $dynComponentDto->routePrefix = $page->slug;

                $this->repository->save($page);
                $isNewPage = true;
            }

            $page ??= $this->repository->findByPrimary($pageId);

            if (!$page || !$page->exists) {
                continue;
            }

            // Create page component
            //
            $pageComponent = false;

            if (!$isNewPage) {
                $pageComponent = $page->components->first(
                    static fn(PageComponent $pageComponent) => $pageComponent->component === $dynComponentDto->className
                );

                if (!$pageComponent && $page->hasDynamicComponent()) {
                    continue;
                }
            }

            if (!$pageComponent) {
                $pageComponent = new PageComponent([
                    'active' => true,
                    'component' => $dynComponentDto->className,
                ]);
                $pageComponent->data->setDefaultValue();
                $pageComponent->page()->associate($page);

                if (!$this->componentRepository->save($pageComponent)) {
                    continue;
                }

                $this->syncPageComponentRoutes(
                    $pageComponent,
                    $dynComponentDto->getPageComponentRoutes($page, $pageComponent)
                );
            }

            $res[$siteId] = $this->syncPageComponentRouteElement(
                $page,
                $pageComponent->routes()->firstOrNew(),
                $elementClassName,
                $elementId,
            );
        }

        return true;
    }

    private function makeUniquePageSlugAndTitle(Page $page): false|Page
    {
        if ($this->repository->pageIsUnique($page)) {
            return $page;
        }

        $origTitle = $page->title;
        $origSlug = $page->slug;
        $isUniquePage = false;

        for ($i = 1; $i <= 5; $i++) {
            $page->title = $origTitle . ' ' . $i;
            $page->slug = $origSlug . '-' . $i;
            if ($this->repository->pageIsUnique($page)) {
                $isUniquePage = true;
                break;
            }
        }

        return $isUniquePage ? $page : false;
    }

    public function syncPageComponentRoutes(PageComponent $pageComponent, Collection $newRoutes): void
    {
        $currentRoutes = $pageComponent->routes;

        if ($currentRoutes->isEmpty()) {
            $currentRoutes = &$newRoutes;
        } else {
            $currentRoutes = $currentRoutes->filter(function (PageComponentRoute $curRoute) use ($newRoutes) {
                $newValueKey = false;
                $newValue = $newRoutes->first(
                    static function (PageComponentRoute $el, $key) use ($curRoute, &$newValueKey) {
                        $newValueKey = $key;
                        return $el->id === $curRoute->id
                            || (
                                $el->page_component_id === $curRoute->page_component_id && $el->path === $curRoute->path
                            );
                    }
                );

                if (!$newValue) {
                    $this->componentRouteRepository->delete($curRoute);

                    return false;
                }

                foreach ($curRoute->getFillable() as $fieldName) {
                    $curRoute->{$fieldName} = $newValue->{$fieldName};
                }

                $newRoutes->forget($newValueKey);

                return $curRoute->isDirty();
            });
        }

        if ($newRoutes->isNotEmpty()) {
            $currentRoutes = $currentRoutes->merge($newRoutes);
        }

        if ($currentRoutes->isEmpty()) {
            return;
        }

        $currentRoutes->each(function (PageComponentRoute $pageComponentRoute) use ($pageComponent) {
            $pageComponentRoute->pageComponent()->associate($pageComponent);
            $this->componentRouteRepository->save($pageComponentRoute);
        });
    }

    public function syncPageComponentRouteElement(
        Page $page,
        PageComponentRoute $componentRoute,
        string $className,
        int|string $elementId,
    ): bool {
        if (!$page->exists) {
            return $this->deletePageComponentRouteElement($page->site_id, $className, $elementId);
        }

        /** @var ?PageComponentRouteElement $element */
        $element = $this->componentRouteElementRepository->getByModelAndElementId($className, $elementId)?->first(
            fn(PageComponentRouteElement $routeEl) => $routeEl->page_component_route_id === $componentRoute->getKey()
                && $page->getKey() === $routeEl->pageComponentRoute->pageComponent->page_id
        );

        if (!$element) {
            $element = new PageComponentRouteElement();
        }

        $element->module_name = resolve(CoreService::class)->getModuleNameByClassNamespace($className);
        $element->model_name = $className;
        $element->element_id = $elementId;
        $element->pageComponentRoute()->associate($componentRoute);

        if (!$element->exists) {
            $this->deletePageComponentRouteElement($page->site_id, $className, $elementId);
        }

        return $element->isDirty() && $this->componentRouteElementRepository->save($element);
    }

    public function getRouteNameByCategory(Collection $routes, int|string $elementKey): ?string
    {
        if ($routes->isEmpty()) {
            return null;
        }

        $cardRoute = $routes->get($elementKey);
        $routeName = null;

        if (is_string($cardRoute)) {
            $routeName = $cardRoute;
        } elseif ($cardRoute instanceof Route) {
            $routeName = $cardRoute->getName();
        }

        return $routeName;
    }

    public function getRouteParams(string|Route $route): array
    {
        if (is_string($route) && mb_strlen($route)) {
            $route = app()->router->getRoutes()->getByName($route);
        }

        return $route?->parameterNames() ?? [];
    }

    public function getPageComponentRouteName(PageComponentRoute $pageComponentRoute): string
    {
        return 'kelnik.pageComponentRoute.' . $pageComponentRoute->getKey();
    }
}
