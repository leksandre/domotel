<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Platform\Exceptions\PageNotFound;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\Services\Contracts\PageService;

interface PagePlatformService
{
    public function __construct(PageRepository $repository, CoreService $coreService, PageService $pageService);

    /**
     * @param int|string $siteId
     * @param int|string $pageId
     * @param array $data
     * @return RedirectResponse
     *
     * @throws PageNotFound
     */
    public function save(int|string $siteId, int|string $pageId, array $data): RedirectResponse;

    /**
     * @param int|string $siteId
     * @param int|string $pageId
     * @return RedirectResponse
     *
     * @throws PageNotFound
     */
    public function delete(int|string $siteId, int|string $pageId): RedirectResponse;

    public function addComponent(int|string $siteId, int|string $pageId, int|string $componentId): bool;

    public function sortComponents(int|string $siteId, int|string $pageId, array $componentsPriority): bool;

    public function createSlugByTitle(string $title): string;

    public function isUnique(int|string $siteId, int|string $pageId, ?string $slug = null): bool;

    public function prepareComponentColorsFromRequest(Collection $colors, array $requestColorValues): Collection;

    public function getBreadcrumbs(Page $page, int $level = 0): array;

    public function getPageUrlById(string|int $primary): string;
}
