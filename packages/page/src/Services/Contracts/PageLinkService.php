<?php

declare(strict_types=1);

namespace Kelnik\Page\Services\Contracts;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Contracts\DynComponentDto;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteElementRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteRepository;
use Kelnik\Page\Repositories\Contracts\PageRepository;

/**
 * Сервис для управления связями динамических модулей со страницами с динамическим компонентом.
 * Например, связь категории публикаций с карточкой публикации.
 * Или связь типа помещения со страницей карточки помещения.
 */
interface PageLinkService
{
    public const PAGE_MODULE_ROW_NEW_PAGE = -1;
    public const PAGE_MODULE_ROW_NO_PAGE = 0;

    public function __construct(
        PageRepository $repository,
        PageComponentRepository $pageComponentRepository,
        PageComponentRouteRepository $pageComponentRouteRepository,
        PageComponentRouteElementRepository $pageComponentRouteElementRepository,
        SiteService $siteService
    );

    /**
     * @param string[] $componentNamespace
     *
     * @return Collection
     */
    public function getOptionListOfPagesWithDynComponent(array $componentNamespace): Collection;

    public function getPagesWithDynComponentByRouteElement(string $classNamespace, int|string $elementId): Collection;

    public function getRouteNameByElement(
        Site $site,
        string $classNamespace,
        int|string $elementId
    ): ?string;

    public function getElementRoutes(Site $site, array $modelElements): Collection;

    public function deletePageComponentRouteElement(int|string $siteId, string $className, int|string $elementId): bool;

    public function createOrUpdateLinkDynComponentToPage(
        array $sitePageId,
        DynComponentDto $dynComponentDto,
        string $elementClassName = null,
        int|string $elementId = null
    ): bool;

    public function syncPageComponentRoutes(PageComponent $pageComponent, Collection $newRoutes): void;

    public function syncPageComponentRouteElement(
        Page $page,
        PageComponentRoute $componentRoute,
        string $className,
        int|string $elementId,
    ): bool;

    public function getRouteNameByCategory(Collection $routes, int|string $elementKey): ?string;

    public function getRouteParams(string|Route $route): array;

    public function getPageComponentRouteName(PageComponentRoute $pageComponentRoute): string;
}
