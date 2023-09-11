<?php

declare(strict_types=1);

namespace Kelnik\Page\Services\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

interface PageService extends PageCache
{
    public const PAGE_ROUTES_CACHE = 'pageRoutes';
    public const ROUTE_PARAM_NAME = 'kelnik_page_id';

    public function __construct(
        PageRepository $repository,
        SiteService $siteService,
        PageLinkService $pageLinkService
    );

    public function getActivePageByPrimary(int|string $siteId, int|string $pageId): Page;

    public function getActivePageWithComponent(int|string $siteId, int|string $pageId, int|string $componentId): array;

    public function getPageByUrl(int|string $siteId, string $path);

    public function getPageContent(Page $page, Request $request, array $stacks = []): array;

    public function getPagesWithoutDynamicComponents(): Collection;

    public function getSitePagesWithoutDynamicComponents(int|string $siteId): Collection;

    public function initViewComponent(
        Page &$page,
        PageComponent &$pageComponent,
        ?Request $request = null
    ): KelnikPageComponent;

    public function loadPageRoutes(): void;

    public function getDynamicPageRouteNameById(int|string $pageId): ?string;

    public function getPageComponentRoutesByPrimaryKey(int|string $pageComponentRouteId): PageComponentRoute;

    public function getPageUrl(Page $page, array $params = [], bool $absolute = true): string;

    public function getPageComponentUrl(
        Page $page,
        PageComponent $pageComponent,
        array $params = [],
        bool $absolute = true
    ): string;

    public function getPageComponentContentAlias(PageComponent $pageComponent, ?Page $page = null): ?string;

    public function prepareComponentColorsFromRequest(Collection $colors, array $requestColorValues): Collection;

    public function getBreadcrumbs(Page $page, int $level = 0): array;

    public function getPageUrlById(int|string $id): string;

    public function getPageRouteName(Page $page): string;
}
